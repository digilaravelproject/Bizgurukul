<?php

namespace App\Services;

use App\Repositories\AffiliateRepository;
use App\Services\AffiliatePermissionService;
use App\Models\AffiliateCommission;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AffiliateService
{
    protected $affiliateRepo;
    protected $permissionService;

    public function __construct(AffiliateRepository $affiliateRepo, AffiliatePermissionService $permissionService)
    {
        $this->affiliateRepo = $affiliateRepo;
        $this->permissionService = $permissionService;
    }

    public function getHistory()
    {
        try {
            return $this->affiliateRepo->getPaginatedCommissions();
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getHistory]: " . $e->getMessage());
            return collect([]); // Return empty collection on failure to prevent UI crashes
        }
    }

    public function processPayout($commissionId)
    {
        try {
            return DB::transaction(function () use ($commissionId) {
                // Safely lock the row using a precise where clause
                $commission = AffiliateCommission::where('id', $commissionId)->lockForUpdate()->first();

                if (!$commission) {
                    throw new Exception("Commission ID {$commissionId} not found.");
                }

                if ($commission->status !== 'pending') {
                    throw new Exception("Commission #{$commissionId} is already processed or invalid.");
                }

                $user = $commission->affiliate;
                $amount = $commission->amount;

                // Calculate new balance (User model has accessor, so we can't write to it directly)
                $newBalance = $user->wallet_balance + $amount;

                // Record the wallet transaction
                $this->affiliateRepo->createWalletTransaction([
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'type'           => 'credit',
                    'balance_after'  => $newBalance,
                    'description'    => "Commission for referral #{$commission->id}",
                    'reference_id'   => $commission->id,
                    'reference_type' => AffiliateCommission::class,
                ]);

                // Update commission status
                $commission->status = 'paid';
                $this->affiliateRepo->saveCommission($commission);

                Log::info("Payout processed successfully for Commission ID: {$commissionId}");
                return true;
            });
        } catch (Exception $e) {
            Log::error("AffiliateService Error [processPayout]: " . $e->getMessage(), [
                'commission_id' => $commissionId
            ]);
            throw $e; // Re-throw so the controller can catch it and show an error to the user
        }
    }

    public function getEarningsStats(User $user)
    {
        try {
            return [
                'today'        => $user->commissions()->whereDate('created_at', Carbon::today())->sum('amount'),
                'last_7_days'  => $user->commissions()->where('created_at', '>=', Carbon::now()->subDays(7))->sum('amount'),
                'last_30_days' => $user->commissions()->where('created_at', '>=', Carbon::now()->subDays(30))->sum('amount'),
                'all_time'     => $user->commissions()->sum('amount'),
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getEarningsStats]: " . $e->getMessage(), ['user_id' => $user->id]);
            // Return safe defaults
            return ['today' => 0, 'last_7_days' => 0, 'last_30_days' => 0, 'all_time' => 0];
        }
    }

    public function getSecondaryStats(User $user)
    {
        try {
            return [
                'pending_earnings' => $user->commissions()->where('status', 'pending')->sum('amount'),
                'total_payouts'    => $user->commissions()->where('status', 'paid')->sum('amount'),
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getSecondaryStats]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['pending_earnings' => 0, 'total_payouts' => 0];
        }
    }

    public function getGraphData(User $user, $days = 30)
    {
        try {
            $startDate = Carbon::now()->subDays($days);

            $earnings = $user->commissions()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $labels = [];
            $data = [];

            // Fill missing dates safely
            for ($i = $days - 1; $i >= 0; $i--) {
                $dateObj = Carbon::now()->subDays($i);
                $labels[] = $dateObj->format('d M');
                $data[] = (float) ($earnings[$dateObj->format('Y-m-d')] ?? 0);
            }

            return ['labels' => $labels, 'data' => $data];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getGraphData]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['labels' => [], 'data' => []];
        }
    }

    public function getCategoryPerformance(User $user)
    {
        try {
            $data = $user->commissions()
                ->where('status', 'paid')
                ->selectRaw('notes, sum(amount) as total')
                ->groupBy('notes')
                ->orderByDesc('total')
                ->take(5)
                ->get();

            return [
                'labels' => $data->map(fn($item) => trim(str_replace(['Commission for Bundle:', 'Commission for Course:'], '', $item->notes)))->toArray(),
                'series' => $data->pluck('total')->toArray()
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getCategoryPerformance]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['labels' => [], 'series' => []];
        }
    }
    public function getDashboardData(User $user)
    {
        try {
            $links = $this->affiliateRepo->getAffiliateLinks($user->id);
            $bundles = $this->affiliateRepo->getAvailableBundles();

            // User request: Bundle permission should always remain enabled for all users.
            $availableBundles = $bundles;

            $canSellCourses = $this->permissionService->canSellCourses($user);
            $availableCourses = $canSellCourses ? $this->affiliateRepo->getAvailableCourses() : collect([]);

            return compact('links', 'availableBundles', 'availableCourses', 'canSellCourses');
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getDashboardData]: " . $e->getMessage());
            throw $e;
        }
    }

    public function generateLink(User $user, array $data)
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                // Permission Check
                if ($data['target_type'] == 'specific_course' && !$this->permissionService->canSellCourses($user)) {
                    throw new Exception('You are not authorized to sell courses.');
                }
                // Bundle permission is now global, no check needed.
                /*
                if ($data['target_type'] == 'bundle') {
                     if (!$this->permissionService->canSellBundle($user, $data['target_id'])) {
                         throw new Exception('You are not authorized to sell this bundle.');
                     }
                }
                */

                // Generate Unique Slug
                $slug = 'ref_' . Str::random(8); // Consider moving slug generation strategy to a helper or config
                while ($this->affiliateRepo->countLinksBySlug($slug) > 0) {
                     $slug = 'ref_' . Str::random(8);
                }

                $linkData = [
                    'user_id' => $user->id,
                    'slug' => $slug,
                    'target_type' => $data['target_type'],
                    'target_id' => $data['target_id'] ?? null,
                    'expires_at' => $data['expires_at'] ?? null,
                    'description' => $data['description'] ?? null,
                ];

                return $this->affiliateRepo->createLink($linkData);
            });
        } catch (Exception $e) {
            Log::error("AffiliateService Error [generateLink]: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteLink(User $user, $id)
    {
        try {
            return DB::transaction(function () use ($user, $id) {
                $link = $this->affiliateRepo->findLink($id, $user->id);

                if (!$link) {
                    throw new Exception('Link not found.');
                }

                $link->is_deleted = true;
                $link->save();

                return true;
            });
        } catch (Exception $e) {
            Log::error("AffiliateService Error [deleteLink]: " . $e->getMessage());
            throw $e;
        }
    }
}
