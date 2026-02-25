<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalRequestedMail;
use App\Services\EmailService;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected WalletRepository $walletRepo;

    public function __construct(WalletService $walletService, WalletRepository $walletRepo)
    {
        $this->walletService = $walletService;
        $this->walletRepo = $walletRepo;
    }

    public function index()
    {
        $user = Auth::user();
        $dashboardData = $this->walletService->getWalletDashboardData($user->id);

        $commissions = $this->walletRepo->getEarnedCommissions($user->id, 10);
        $withdrawals = $this->walletRepo->getWithdrawalRequests($user->id, 10);

        return view('student.wallet.index', compact('dashboardData', 'commissions', 'withdrawals'));
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:affiliate_commissions,id'
        ]);

        try {
            $this->walletService->requestWithdrawal(Auth::id(), $request->commission_ids);

            // Notify admin of withdrawal request
            try {
                $user = Auth::user();
                $adminEmail = EmailService::adminEmail();
                if ($adminEmail) {
                    $bankDetail = $user->bankDetail;
                    $bankDetails = $bankDetail
                        ? "{$bankDetail->bank_name} — A/C: {$bankDetail->account_number} (IFSC: {$bankDetail->ifsc_code})"
                        : 'Bank details not on file';
                    $totalAmount = \App\Models\AffiliateCommission::whereIn('id', $request->commission_ids)->sum('amount');
                    Mail::to($adminEmail)->queue(new WithdrawalRequestedMail(
                        $user->name,
                        $user->email,
                        number_format($totalAmount, 2),
                        $bankDetails
                    ));
                }
            } catch (\Throwable $ignored) {}

            return back()->with('success', 'Withdrawal request submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
