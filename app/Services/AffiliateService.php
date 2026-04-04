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
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'balance_after' => $newBalance,
                    'description' => "Commission for referral #{$commission->id}",
                    'reference_id' => $commission->id,
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
                'today' => $user->commissions()->whereDate('created_at', Carbon::today())->sum('amount'),
                'last_7_days' => $user->commissions()->where('created_at', '>=', Carbon::today()->subDays(6))->sum('amount'),
                'last_30_days' => $user->commissions()->where('created_at', '>=', Carbon::today()->subDays(29))->sum('amount'),
                'all_time' => $user->commissions()->sum('amount'),
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
                'total_payouts' => $user->commissions()->where('status', 'paid')->sum('amount'),
                'wallet_balance' => $user->wallet_balance,
                'total_withdrawn' => \App\Models\WithdrawalRequest::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount'),
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getSecondaryStats]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['pending_earnings' => 0, 'total_payouts' => 0, 'wallet_balance' => 0, 'total_withdrawn' => 0];
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
                'series' => $data->pluck('total')->map(fn($v) => (float)$v)->toArray()
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getCategoryPerformance]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['labels' => [], 'series' => []];
        }
    }

    /**
     * Get bundle-wise sales distribution for the student.
     */
    public function getBundleDistribution(User $user): array
    {
        try {
            $commissions = $user->commissions()
                ->with(['reference'])
                ->get();

            $data = $commissions->map(function ($commission) {
                $title = 'Unknown Bundle';
                $bundleId = null;

                // 1. Try to get info from reference (Bundle or Payment)
                if ($commission->reference instanceof \App\Models\Bundle) {
                    $title = $commission->reference->title;
                    $bundleId = $commission->reference->id;
                } elseif ($commission->reference instanceof \App\Models\Payment && $commission->reference->bundle) {
                    $title = $commission->reference->bundle->title;
                    $bundleId = $commission->reference->bundle->id;
                } 
                // 2. Fallback: Parse from notes if reference loading fails
                elseif (!empty($commission->notes) && str_contains($commission->notes, 'Commission for Bundle:')) {
                    $title = trim(str_replace('Commission for Bundle:', '', $commission->notes));
                    // We use the title as a grouping key if ID is missing
                    $bundleId = 'name_' . $title;
                }

                return [
                    'bundle_id' => $bundleId,
                    'title'     => $title,
                    'amount'    => (float) $commission->amount,
                ];
            })
            ->filter(fn($item) => $item['bundle_id'] !== null)
            ->groupBy('bundle_id')
            ->map(function ($group) {
                return [
                    'title'       => $group->first()['title'],
                    'sales_count' => $group->count(),
                    'revenue'     => $group->sum('amount'),
                ];
            })
            ->sortByDesc('sales_count')
            ->values();

            return [
                'labels'  => $data->pluck('title')->toArray(),
                'series'  => $data->pluck('sales_count')->toArray(),
                'revenue' => $data->pluck('revenue')->toArray(),
                'stats'   => $data->toArray(),
            ];
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getBundleDistribution]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['labels' => [], 'series' => [], 'revenue' => [], 'stats' => []];
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

    public function getLeaderboard($filter = 'last_30_days', $limit = 10)
    {
        try {
            $query = AffiliateCommission::query()
                ->whereHas('affiliate', function ($q) {
                    $q->where('hide_from_leaderboard', '!=', 1);
                })
                ->selectRaw('affiliate_id, SUM(amount) as total_earnings')
                ->groupBy('affiliate_id')
                ->with([
                    'affiliate' => function ($query) {
                        $query->select('id', 'name', 'profile_picture', 'profile_photo_url');
                    }
                ]);

            if ($filter === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'last_7_days') {
                $query->where('created_at', '>=', Carbon::today()->subDays(6));
            } elseif ($filter === 'last_30_days') {
                $query->where('created_at', '>=', Carbon::today()->subDays(29));
            } elseif ($filter === 'this_year') {
                // YEARLY FILTER ADDED HERE
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
                // Note: For 'last 365 days', use 'Carbon::now()->subDays(365)'
            } // 'all_time' no date filter

            return $query->orderByDesc('total_earnings')
                ->take($limit)
                ->get();
        } catch (Exception $e) {
            Log::error("AffiliateService Error [getLeaderboard]: " . $e->getMessage());
            return collect([]);
        }
    }

    public function getUserRank(User $user, $filter = 'last_30_days')
    {
        try {
            $userEarningsQuery = $user->commissions();

            if ($filter === 'today') {
                $userEarningsQuery->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'last_7_days') {
                $userEarningsQuery->where('created_at', '>=', Carbon::today()->subDays(6));
            } elseif ($filter === 'last_30_days') {
                $userEarningsQuery->where('created_at', '>=', Carbon::today()->subDays(29));
            } elseif ($filter === 'this_year') {
                // YEARLY FILTER ADDED HERE
                $userEarningsQuery->where('created_at', '>=', Carbon::now()->startOfYear());
            }

            $userEarnings = (float) $userEarningsQuery->clone()->sum('amount');
            $userSaleCount = $userEarningsQuery->clone()->count();

            $query = AffiliateCommission::query()
                ->whereHas('affiliate', function ($q) {
                    $q->where('hide_from_leaderboard', '!=', 1);
                })
                ->selectRaw('affiliate_id, SUM(amount) as total_earnings')
                ->groupBy('affiliate_id');

            if ($filter === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'last_7_days') {
                $query->where('created_at', '>=', Carbon::today()->subDays(6));
            } elseif ($filter === 'last_30_days') {
                $query->where('created_at', '>=', Carbon::today()->subDays(29));
            } elseif ($filter === 'this_year') {
                // YEARLY FILTER ADDED HERE
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
            }

            $usersWithMoreEarnings = DB::query()
                ->fromSub($query, 'earnings_table')
                ->where('earnings_table.total_earnings', '>', $userEarnings)
                ->count();

            $rank = $usersWithMoreEarnings + 1;

            return [
                'rank' => $rank,
                'earnings' => $userEarnings,
                'sale_count' => $userSaleCount
            ];

        } catch (Exception $e) {
            Log::error("AffiliateService Error [getUserRank]: " . $e->getMessage(), ['user_id' => $user->id]);
            return ['rank' => 0, 'earnings' => 0, 'sale_count' => 0];
        }
    }
}
