<?php

namespace App\Exports;

use App\Models\AffiliateCommission;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AffiliateHistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build the query for affiliate commissions export.
     */
    public function query()
    {
        $query = AffiliateCommission::with(['affiliate', 'referredUser', 'reference']);

        // Applying Search (Affiliate name/email/code, or referred user name/email)
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('affiliate', function ($aq) use ($search) {
                    $aq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('referral_code', 'like', "%{$search}%");
                })->orWhereHas('referredUser', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Applying Date Filters (Using IST / Asia/Kolkata boundary)
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
            'Conversion ID',
            'Date (IST)',
            'Time (IST)',
            'Date & Time (IST)',
            'Affiliate Name',
            'Affiliate Code',
            'Affiliate Email',
            'Affiliate Mobile',
            'Referred User Name',
            'Referred User Email',
            'Product / Course',
            'Commission (₹)',
            'Status',
        ];
    }

    /**
     * Map each affiliate commission row.
     */
    public function map($commission): array
    {
        // Exact IST conversion (+05:30)
        $ist = $commission->created_at ? $commission->created_at->copy()->timezone('Asia/Kolkata') : null;

        $statusLabel = match ($commission->status) {
            'on_hold'   => 'On Hold',
            'available' => 'Available',
            'requested' => 'Processing Withdrawal',
            'pending'   => 'Pending',
            'paid'      => 'Paid',
            default     => ucfirst(str_replace('_', ' ', $commission->status ?? 'N/A')),
        };

        $productTitle = $commission->reference ? ($commission->reference->title ?? 'N/A') : ('Product ID: ' . ($commission->reference_id ?? 'N/A'));

        return [
            $commission->id,
            $ist ? $ist->format('d M Y') : 'N/A',
            $ist ? $ist->format('h:i A') : 'N/A',
            $ist ? $ist->format('d M Y, h:i A') : 'N/A',
            $commission->affiliate->name ?? 'Unknown',
            $commission->affiliate->referral_code ?? 'N/A',
            $commission->affiliate->email ?? 'N/A',
            $commission->affiliate->mobile ?? 'N/A',
            $commission->referredUser->name ?? 'Unknown',
            $commission->referredUser->email ?? 'N/A',
            $productTitle,
            number_format((float) $commission->amount, 2, '.', ''),
            $statusLabel,
        ];
    }

    /**
     * Style worksheet headers.
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
