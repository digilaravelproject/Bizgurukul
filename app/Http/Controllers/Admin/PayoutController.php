<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Repositories\WalletRepository;

class PayoutController extends Controller
{
    protected WalletService $walletService;
    protected WalletRepository $walletRepo;

    public function __construct(WalletService $walletService, WalletRepository $walletRepo)
    {
        $this->walletService = $walletService;
        $this->walletRepo = $walletRepo;
    }

    public function index(Request $request)
    {
        $withdrawals = $this->walletRepo->getAllWithdrawalRequests(20);
        return view('admin.payouts.index', compact('withdrawals'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'reference_number' => 'nullable|string',
            'payment_method' => 'required|string',
            'admin_note' => 'nullable|string',
        ]);

        try {
            $this->walletService->approveWithdrawal($id, $request->all());
            return back()->with('success', 'Withdrawal approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string',
        ]);

        try {
            $this->walletService->rejectWithdrawal($id, $request->admin_note);
            return back()->with('success', 'Withdrawal rejected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function earlyApproveCommission($id)
    {
        try {
            $this->walletService->manuallyApproveCommission($id);
            return back()->with('success', 'Commission manually approved and made available.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
