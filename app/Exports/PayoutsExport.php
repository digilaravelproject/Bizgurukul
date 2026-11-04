<?php

namespace App\Exports;

use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayoutsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build the query for withdrawal/payout requests.
     */
    public function query()
    {
        $query = WithdrawalRequest::with(['user.bank', 'user.kyc', 'commissions']);

        // Search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
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

        // Status filter
        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        // Date range filters (in IST)
        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;

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

        return $query->latest();
    }

    /**
     * Column headers for Excel file.
     */
    public function headings(): array
    {
        return [
            'Payout ID',
            'Request Date (IST)',
            'Disbursement Date (IST)',
            'Partner Name',
            'Partner Email',
            'Partner Mobile',
            'PAN Name',
            'Bank Name',
            'Account Holder Name',
            'Account Number',
            'IFSC Code',
            'UPI ID',
            'Gross Amount (₹)',
            'TDS Deducted (₹)',
            'Net Disbursed Amount (₹)',
            'Payment Method',
            'Bank UTR / Transaction ID',
            'Status',
            'Admin Note'
        ];
    }

    /**
     * Map each row for Excel.
     */
    public function map($withdrawal): array
    {
        $createdIst = $withdrawal->created_at ? $withdrawal->created_at->copy()->timezone('Asia/Kolkata') : null;
        
        // Use process_at or updated_at if approved/processed
        $disbursedAt = null;
        if (in_array($withdrawal->status, ['approved', 'paid'])) {
            $dateSource = $withdrawal->process_at ?? $withdrawal->updated_at;
            $disbursedAt = $dateSource ? Carbon::parse($dateSource)->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'N/A';
        } else {
            $disbursedAt = 'Pending / Not Disbursed';
        }

        $bank = $withdrawal->user ? $withdrawal->user->bank : null;
        $kyc = $withdrawal->user ? $withdrawal->user->kyc : null;

        $statusLabel = match ($withdrawal->status) {
            'approved'   => 'Approved / Paid',
            'pending'    => 'Pending',
            'processing' => 'Processing',
            'rejected'   => 'Rejected',
            default      => ucfirst($withdrawal->status ?? 'N/A')
        };

        return [
            '#' . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT),
            $createdIst ? $createdIst->format('d M Y, h:i A') : 'N/A',
            $disbursedAt,
            $withdrawal->user->name ?? 'Unknown',
            $withdrawal->user->email ?? 'N/A',
            $withdrawal->user->mobile ?? 'N/A',
            $kyc->pan_name ?? 'N/A',
            $bank->bank_name ?? 'N/A',
            $bank->account_holder_name ?? ($withdrawal->user->name ?? 'N/A'),
            $bank ? (string)$bank->account_number : 'N/A',
            $bank->ifsc_code ?? 'N/A',
            $bank->upi_id ?? 'N/A',
            number_format((float)$withdrawal->amount, 2, '.', ''),
            number_format((float)$withdrawal->tds_deducted, 2, '.', ''),
            number_format((float)$withdrawal->payable_amount, 2, '.', ''),
            strtoupper($withdrawal->payment_method ?? 'N/A'),
            $withdrawal->transaction_id ?? ($withdrawal->reference_number ?? 'N/A'),
            $statusLabel,
            $withdrawal->admin_note ?? ''
        ];
    }

    /**
     * Header row styling.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'] // Dark slate
                ]
            ],
        ];
    }
}
