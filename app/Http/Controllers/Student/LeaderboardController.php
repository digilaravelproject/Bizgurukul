<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class LeaderboardController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  Cache Settings
    // ─────────────────────────────────────────────────────────────

    /** Cache duration: 12 hours (in seconds) */
    private const CACHE_TTL = 43200;

    /** Valid filter values accepted from the frontend */
    private const ALLOWED_FILTERS = ['today', 'last_7_days', 'last_30_days', 'this_year', 'all_time'];

    // ─────────────────────────────────────────────────────────────
    //  Constructor
    // ─────────────────────────────────────────────────────────────

    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    // ─────────────────────────────────────────────────────────────
    //  Public Routes
    // ─────────────────────────────────────────────────────────────

    /**
     * Render the leaderboard page.
     */
    public function index(Request $request)
    {
        return view('student.leaderboard');
    }

    /**
     * Fetch leaderboard data (AJAX endpoint).
     *
     * Cache Strategy:
     *  - Leaderboard top-10  → shared cache per filter  (all users get same data)
     *  - User rank           → private cache per user+filter
     *  - On new data arrival → both caches are forgotten and rebuilt fresh
     */
    public function fetchData(Request $request)
    {
        try {
            $filter = $this->resolveFilter($request->get('filter'));
            $user   = Auth::user();

            // ── Cache Keys ─────────────────────────────────────────
            $leaderboardKey = $this->leaderboardCacheKey($filter);
            $userRankKey    = $this->userRankCacheKey($user->id, $filter);

            // ── Leaderboard Cache (shared for all users) ───────────
            $formattedLeaderboard = Cache::remember(
                $leaderboardKey,
                self::CACHE_TTL,
                function () use ($filter) {
                    $leaderboard = $this->affiliateService->getLeaderboard($filter, 10);

                    return $leaderboard->map(fn ($item, $index) => [
                        'rank'            => $index + 1,
                        'name'            => $item->affiliate->name ?? 'Unknown',
                        'profile_picture' => $item->affiliate->profile_image_url ?? null,
                        'earnings'        => (float) $item->total_earnings,
                    ])->values();
                }
            );

            // ── User Rank Cache (private per user) ─────────────────
            $userRankData = Cache::remember(
                $userRankKey,
                self::CACHE_TTL,
                fn () => $this->affiliateService->getUserRank($user, $filter)
            );

            // ── JSON Response ──────────────────────────────────────
            return response()->json([
                'success'              => true,
                'filter'               => $filter,
                'leaderboard'          => $formattedLeaderboard,
                'user_rank'            => $userRankData['rank']       ?? 0,
                'user_earnings'        => (float) ($userRankData['earnings']   ?? 0),
                'user_sale_count'      => (int)   ($userRankData['sale_count'] ?? 0),
                'user_name'            => $user->name,
                'user_profile_picture' => $user->profile_image_url ?? null,
                'cached_at'            => now()->toDateTimeString(),
            ]);

        } catch (Exception $e) {
            Log::error('LeaderboardController@fetchData: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'filter'  => $request->get('filter'),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Leaderboard data load karne mein problem aayi. Please retry.',
            ], 500);
        }
    }

    /**
     * Manually bust the leaderboard cache for a given filter.
     *
     * Call this method whenever new commission/sales data is recorded
     * so stale cache is replaced with fresh data on the next request.
     *
     * Usage (from any service/observer):
     *   app(LeaderboardController::class)->bustCache('last_30_days', $userId);
     */
    public static function bustCache(?string $filter = null, ?int $userId = null): void
    {
        $filters = $filter ? [$filter] : self::ALLOWED_FILTERS;

        foreach ($filters as $f) {
            // Clear shared leaderboard cache
            Cache::forget('leaderboard_data_' . $f);

            // Clear specific user rank cache if userId provided
            if ($userId) {
                Cache::forget('leaderboard_user_rank_' . $userId . '_' . $f);
            }

            Log::info('LeaderboardController: Cache busted', [
                'filter'  => $f,
                'user_id' => $userId ?? 'all',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Private Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Validate and return an allowed filter value.
     * Falls back to 'last_30_days' for unknown/missing values.
     */
    private function resolveFilter(?string $filter): string
    {
        return in_array($filter, self::ALLOWED_FILTERS, true)
            ? $filter
            : 'last_30_days';
    }

    /** Cache key for the shared leaderboard top-10. */
    private function leaderboardCacheKey(string $filter): string
    {
        return 'leaderboard_data_' . $filter;
    }

    /** Cache key for an individual user's rank data. */
    private function userRankCacheKey(int $userId, string $filter): string
    {
        return 'leaderboard_user_rank_' . $userId . '_' . $filter;
    }
}
