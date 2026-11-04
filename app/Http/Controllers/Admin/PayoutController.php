<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PayoutsExport;
use App\Http\Controllers\Controller;
use App\Mail\WithdrawalApprovedMail;
use App\Models\WithdrawalRequest;
use App\Repositories\WalletRepository;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

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
        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100, 200])) {
            $perPage = 20;
        }

        $query = WithdrawalRequest::with(['user.bank', 'user.kyc', 'commissions']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
            });
        }

        // Status
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Date Range (IST)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay(),
                Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay());
        } elseif ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay());
        }

        $withdrawals = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.payouts.partials.payout_table', compact('withdrawals'))->render();
        }

        return view('admin.payouts.index', compact('withdrawals'));
    }

    public function export(Request $request)
    {
        $filters = [
            'search'     => $request->input('search'),
            'status'     => $request->input('status'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ];

        $filename = 'disbursement_payouts_report_' . Carbon::now('Asia/Kolkata')->format('Y-m-d_h-i-s_A') . '.xlsx';

        return Excel::download(new PayoutsExport($filters), $filename);
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

            // Notify user of approval
            try {
                $withdrawal = \App\Models\WithdrawalRequest::with('user.bankDetail')->find($id);
                if ($withdrawal && $withdrawal->user) {
                    $user = $withdrawal->user;
                    $bankName = $user->bankDetail ? $user->bankDetail->bank_name : $request->input('payment_method', 'Bank Account');
                    Mail::to($user->email)->queue(new WithdrawalApprovedMail(
                        $user->name,
                        number_format($withdrawal->amount, 2),
                        $bankName
                    ));
                }
            } catch (\Throwable $ignored) {}

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

    public function earlyApproveCommission(Request $request, $id)
    {
        try {
            $this->walletService->manuallyApproveCommission($id);
            if ($request->ajax()) {
                return response()->json(['status' => true, 'message' => 'Commission manually approved and made available.']);
            }
            return back()->with('success', 'Commission manually approved and made available.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkNew()
    {
        try {
            $latestId = \App\Models\WithdrawalRequest::max('id') ?? 0;
            return response()->json([
                'status' => true,
                'latest_id' => $latestId
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
