<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\Bundle;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LeadsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Lead::with('sponsor');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Mobile',
            'Gender',
            'Sponsor Code',
            'Sponsor Name',
            'Product Preference',
            'City',
            'State',
            'IP Address',
            'Created At',
        ];
    }

    public function map($lead): array
    {
        // Resolve Product Preference
        $productName = 'N/A';
        $bundleId = $lead->product_preference['bundle_id'] ?? null;
        if ($bundleId) {
            $bundle = Bundle::find($bundleId);
            $productName = $bundle ? $bundle->title : 'Unknown Bundle';
        }

        return [
            $lead->id,
            $lead->name,
            $lead->email,
            $lead->mobile,
            ucfirst($lead->gender),
            $lead->referral_code,
            $lead->sponsor ? $lead->sponsor->name : 'N/A',
            $productName,
            $lead->city,
            $lead->state,
            $lead->ip_address,
            $lead->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
