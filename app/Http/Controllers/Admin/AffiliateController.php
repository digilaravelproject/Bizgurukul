<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Exception;

class AffiliateController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function history()
    {
        $commissions = $this->affiliateService->getHistory();
        return view('admin.affiliate.history', compact('commissions'));
    }

    /**
     * Mark Commission as Paid (Approve & Credit Wallet)
     */
    public function markAsPaid($id)
    {
        try {
            $this->affiliateService->processPayout($id);
            return redirect()->back()->with('success', 'Commission approved and wallet credited.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
