<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = User::with(['referrer', 'state', 'roles']);

        if (!empty($this->filters['trash']) && $this->filters['trash'] === 'true') {
            $query->onlyTrashed();
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
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
            'Referral Code',
            'Sponsor Name',
            'Sponsor Code',
            'Roles',
            'KYC Status',
            'Bank Status',
            'State',
            'City',
            'Actions (Banned)',
            'Joined At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->mobile,
            $user->referral_code,
            $user->referrer ? $user->referrer->name : 'N/A',
            $user->referrer ? $user->referrer->referral_code : 'N/A',
            $user->getRoleNames()->implode(', '),
            strtoupper($user->kyc_status),
            strtoupper($user->bank_status ?: 'not_submitted'),
            $user->state ? $user->state->name : 'N/A',
            $user->city,
            $user->is_banned ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
