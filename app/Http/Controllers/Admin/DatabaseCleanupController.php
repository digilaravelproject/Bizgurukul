<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DatabaseCleanupController extends Controller
{
    /**
     * Protected admin emails – these users will NEVER be deleted.
     */
    private array $protectedEmails = [
        'admin@admin.com',
        'vinayshrivardhankar9@gmail.com',
        'shaikhzaid468@gmail.com',
    ];

    /**
     * Show the cleanup page with current record counts.
     */
    public function index()
    {
        $protectedUserIds = DB::table('users')
            ->whereIn('email', $this->protectedEmails)
            ->pluck('id')
            ->toArray();

        $tablesInfo = $this->getTablesInfo($protectedUserIds);

        return view('admin.database-cleanup', [
            'tables'           => $tablesInfo,
            'protectedEmails'  => $this->protectedEmails,
            'protectedUserIds' => $protectedUserIds,
            'cleaned'          => false,
            'results'          => [],
        ]);
    }

    /**
     * Execute the cleanup and display results.
     */
    public function execute()
    {
        $results = [];
        $startTime = microtime(true);

        // Get protected user IDs
        $protectedUserIds = DB::table('users')
            ->whereIn('email', $this->protectedEmails)
            ->pluck('id')
            ->toArray();

        if (empty($protectedUserIds)) {
            return redirect()->back()->with('error', 'Critical Error: Admin users not found. Cleanup aborted for safety.');
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // 1. Tables to completely TRUNCATE (Full wipe)
            $truncateTables = [
                'activity_log',
                'affiliate_commissions',
                'affiliate_links',
                'bank_details',
                'bank_update_requests',
                'coupon_transfers',
                'coupons',
                'kyc_details',
                'leads',
                'payments',
                'referral_visits',
                'user_achievements',
                'user_affiliate_settings',
                'video_progress',
                'wallet_transactions',
                'withdrawal_requests',
                'course_user',
                'certificates',
                'commission_rules',
                'personal_access_tokens',
                'sessions',
                'contact_inquiries',
                'failed_jobs',
                'jobs',
                'job_batches',
            ];

            foreach ($truncateTables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    DB::table($table)->truncate();
                    $results[] = [
                        'table' => $table,
                        'deleted' => $count,
                        'status' => 'success',
                        'message' => 'Table truncated successfully.'
                    ];
                }
            }

            // 2. Special handling for USERS (Keep Admins)
            // Remove roles/permissions for everyone EXCEPT admins
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->whereNotIn('model_id', $protectedUserIds)
                ->delete();

            DB::table('model_has_permissions')
                ->where('model_type', 'App\\Models\\User')
                ->whereNotIn('model_id', $protectedUserIds)
                ->delete();

            // Clear cache and password tokens for test users
            DB::table('password_reset_tokens')
                ->whereNotIn('email', $this->protectedEmails)
                ->delete();

            // Finally delete Users
            $userCount = DB::table('users')->whereNotIn('id', $protectedUserIds)->count();
            DB::table('users')->whereNotIn('id', $protectedUserIds)->delete();

            $results[] = [
                'table' => 'users',
                'deleted' => $userCount,
                'status' => 'success',
                'message' => 'Test users removed. Kept ' . count($protectedUserIds) . ' admin(s).'
            ];

        } catch (\Exception $e) {
            Log::error("Cleanup Error: " . $e->getMessage());
            $results[] = [
                'table' => 'SYSTEM ERROR',
                'deleted' => 0,
                'status' => 'danger',
                'message' => $e->getMessage()
            ];
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // 3. Clear all caches
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Wipe DB cache tables
            if (Schema::hasTable('cache')) DB::table('cache')->truncate();
            if (Schema::hasTable('cache_locks')) DB::table('cache_locks')->truncate();

            $results[] = [
                'table' => 'Application Cache',
                'deleted' => 0,
                'status' => 'info',
                'message' => 'System cache, routes, and config cleared.'
            ];
        } catch (\Exception $e) {
            $results[] = [
                'table' => 'Cache Clear',
                'deleted' => 0,
                'status' => 'warning',
                'message' => 'Artisan cache clear failed: ' . $e->getMessage()
            ];
        }

        $totalTime = round(microtime(true) - $startTime, 2);

        return view('admin.database-cleanup', [
            'tables'           => $this->getTablesInfo($protectedUserIds),
            'protectedEmails'  => $this->protectedEmails,
            'protectedUserIds' => $protectedUserIds,
            'cleaned'          => true,
            'results'          => $results,
            'totalTime'        => $totalTime,
        ]);
    }

    private function getTablesInfo(array $protectedUserIds): array
    {
        $tables = [];

        $toClear = [
            'activity_log', 'affiliate_commissions', 'affiliate_links', 'bank_details',
            'bank_update_requests', 'coupon_transfers', 'coupons', 'kyc_details',
            'leads', 'payments', 'referral_visits', 'user_achievements',
            'user_affiliate_settings', 'video_progress', 'wallet_transactions',
            'withdrawal_requests', 'course_user', 'certificates', 'commission_rules',
            'personal_access_tokens', 'sessions', 'contact_inquiries'
        ];

        $toKeep = [
            'courses', 'lessons', 'bundles', 'bundle_items', 'coupon_packages',
            'categories', 'settings', 'states', 'roles', 'permissions',
            'role_has_permissions', 'email_templates', 'taxes', 'achievements',
            'beginner_guide_videos', 'course_resources', 'communities', 'migrations'
        ];

        foreach ($toClear as $name) {
            if (Schema::hasTable($name)) {
                $tables[] = [
                    'name' => $name,
                    'count' => DB::table($name)->count(),
                    'type' => 'cleanup',
                    'action' => 'Full Wipe'
                ];
            }
        }

        // Special User entry
        $tables[] = [
            'name' => 'users',
            'count' => DB::table('users')->count(),
            'type' => 'partial',
            'action' => 'Wipe All EXCEPT Admins'
        ];

        foreach ($toKeep as $name) {
            if (Schema::hasTable($name)) {
                $tables[] = [
                    'name' => $name,
                    'count' => DB::table($name)->count(),
                    'type' => 'safe',
                    'action' => 'Protected'
                ];
            }
        }

        return $tables;
    }
}
