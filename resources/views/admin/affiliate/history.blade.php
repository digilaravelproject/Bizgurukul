@extends('layouts.admin')

@section('title', 'Referral History')

@section('header')
    <div class="flex justify-between items-center w-full">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Referral & Commission History') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Recent Conversions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Affiliate</th>
                            <th class="px-6 py-4">Referred User</th>
                            <th class="px-6 py-4">Product</th>
                            <th class="px-6 py-4">Commission</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($commissions as $commission)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $commission->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        {{ $commission->affiliate->name ?? 'Unknown' }}
                                        <span class="text-xs text-slate-400 ml-1">({{ $commission->affiliate->referral_code ?? 'N/A' }})</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $commission->referredUser->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($commission->reference)
                                        {{ $commission->reference->title ?? 'Product ID: '.$commission->reference_id }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    ₹{{ number_format($commission->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="capitalize px-2 py-1 rounded-full text-xs font-bold
                                        {{ $commission->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $commission->status }}
                                    </span>

                                    @if($commission->status == 'pending')
                                        <form action="{{ route('admin.affiliate.commission.pay', $commission->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Approve payment and credit wallet?');">
                                            @csrf
                                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900 font-bold underline">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                    No referral history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="p-4 border-t border-slate-100">
                {{ $commissions->links() }}
            </div>
        </div>

    </div>
@endsection
