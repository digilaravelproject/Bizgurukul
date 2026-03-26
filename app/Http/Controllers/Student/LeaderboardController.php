<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class LeaderboardController extends Controller
{
    /** Valid filter values accepted from the frontend */
    private const ALLOWED_FILTERS = ['last_7_days', 'last_30_days', 'this_year', 'all_time'];

    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Render the leaderboard page.
     */
    public function index(Request $request)
    {
        return view('student.leaderboard');
    }

    /**
     * Fetch leaderboard data (AJAX endpoint).
     * Root Solution: Real-time data fetching, efficient queries, no caching overhead.
     */
    public function fetchData(Request $request)
    {
        try {
            $filter = $this->resolveFilter($request->input('filter'));
            $user   = Auth::user();

            // Fetch Leaderboard (Directly, no cache for real-time accuracy)
            $leaderboard = $this->affiliateService->getLeaderboard($filter, 10);
            $formattedLeaderboard = $leaderboard->map(fn ($item, $index) => [
                'rank'            => $index + 1,
                'name'            => $item->affiliate->name ?? 'Unknown',
                'profile_picture' => $item->affiliate->profile_image_url ?? null,
                'earnings'        => (float) $item->total_earnings,
            ])->values();

            // Fetch User Rank (Directly)
            $userRankData = $this->affiliateService->getUserRank($user, $filter);

            return response()->json([
                'success'              => true,
                'filter'               => $filter,
                'leaderboard'          => $formattedLeaderboard,
                'user_rank'            => $userRankData['rank']       ?? 0,
                'user_earnings'        => (float) ($userRankData['earnings']   ?? 0),
                'user_sale_count'      => (int)   ($userRankData['sale_count'] ?? 0),
                'user_name'            => $user->name,
                'user_profile_picture' => $user->profile_image_url ?? null,
                'fetched_at'           => now()->toDateTimeString(),
            ]);

        } catch (Exception $e) {
            Log::error('LeaderboardController@fetchData: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'filter'  => $request->input('filter'),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load leaderboard data. Please retry.',
            ], 500);
        }
    }

    /**
     * Validate and return an allowed filter value.
     */
    private function resolveFilter(?string $filter): string
    {
        return in_array($filter, self::ALLOWED_FILTERS, true)
            ? $filter
            : 'last_30_days';
    }
}
