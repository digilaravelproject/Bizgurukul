<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class LeaderboardController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function index(Request $request)
    {
        return view('student.leaderboard');
    }

    public function fetchData(Request $request)
    {
        try {
            $filter = $request->get('filter', 'last_30_days');
            $user = Auth::user();

            $leaderboard = $this->affiliateService->getLeaderboard($filter, 10);
            $userRankData = $this->affiliateService->getUserRank($user, $filter);

            // Format data for frontend
            $formattedLeaderboard = $leaderboard->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $item->affiliate->name,
                    'profile_picture' => $item->affiliate->profile_image_url,
                    'earnings' => $item->total_earnings,
                ];
            });

            return response()->json([
                'success' => true,
                'leaderboard' => $formattedLeaderboard,
                'user_rank' => $userRankData['rank'],
                'user_earnings' => $userRankData['earnings'],
                'user_sale_count' => $userRankData['sale_count'] ?? 0,
                'user_name' => $user->name,
                'user_profile_picture' => $user->profile_image_url,
            ]);

        } catch (Exception $e) {
            Log::error("LeaderboardController Error [fetchData]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch leaderboard data.'
            ], 500);
        }
    }
}
