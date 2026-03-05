<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Payment;
use Carbon\Carbon;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Payment::with(['user', 'bundle', 'course', 'paymentable']);

        // Applying Search
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            })->orWhere('razorpay_order_id', 'like', "%{$search}%")
              ->orWhere('razorpay_payment_id', 'like', "%{$search}%");
        }

        // Applying Date Filter
        $filter = $this->filters['filter'] ?? 'all_time';
        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;

        if ($filter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        } else {
            switch ($filter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case '7_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                case '30_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        return $query->latest();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Invoice No.',
            'Invoice Date',
            'Party Name',
            'SCN/ HSN',
            'Amount',
            'CGST',
            'SGST',
            'Total Amount'
        ];
    }

    /**
     * @param mixed $order
     * @return array
     */
    public function map($order): array
    {
        $totalAmount = $order->total_amount ?? $order->amount;
        $taxAmount = $order->tax_amount ?? 0;
        $baseAmount = $totalAmount - $taxAmount;

        // Split tax 50/50
        $cgst = round($taxAmount / 2, 2);
        $sgst = round($taxAmount / 2, 2);

        return [
            $order->invoice_no,
            $order->created_at->format('d-m-Y'),
            $order->user->name ?? 'N/A',
            '999293', // Hardcoded HSN (updated by user)
            number_format($baseAmount, 2, '.', ''),
            number_format($cgst, 2, '.', ''),
            number_format($sgst, 2, '.', ''),
            number_format($totalAmount, 2, '.', '')
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
