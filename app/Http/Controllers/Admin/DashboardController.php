<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stats = $this->dashboardService->getAggregateStats();
        return view('admin.dashboard', compact('stats'));
    }

    public function stats(Request $request)
    {
        $period = $request->input('period', 'month');
        return response()->json([
            'aggregate' => $this->dashboardService->getAggregateStats(),
            'chart' => $this->dashboardService->getSalesChartData($period),
        ]);
    }
}
