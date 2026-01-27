@extends('layouts.admin')

@section('title', 'Admin Control Panel')

{{-- Header Section --}}
@section('header')
<div class="flex justify-between items-center w-full">
    <h2 class="font-bold text-xl text-slate-800 leading-tight">
        {{ __('Admin Control Panel') }}
    </h2>

    <div
        class="hidden sm:flex items-center text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
        <span class="mr-2">System Time:</span>
        <span class="text-slate-800">{{ now()->format('d M, H:i') }}</span>
    </div>
</div>
@endsection

{{-- Page Content --}}
@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- 1. Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Total Students --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">
                    Total Students
                </div>
                <span class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <!-- icon -->
                </span>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">
                {{ number_format($totalStudents) }}
            </div>
        </div>

        {{-- Paid Commissions --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">
                    Paid Commissions
                </div>
                <span class="p-2 bg-green-50 rounded-lg text-green-600"></span>
            </div>
            <div class="text-3xl font-extrabold text-green-600 mt-2">
                ₹{{ number_format($totalCommissionsPaid, 2) }}
            </div>
        </div>

        {{-- Pending Payouts --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">
                    Pending Payouts
                </div>
                <span class="p-2 bg-orange-50 rounded-lg text-orange-600"></span>
            </div>
            <div class="text-3xl font-extrabold text-orange-500 mt-2">
                ₹{{ number_format($pendingCommissions, 2) }}
            </div>
        </div>
    </div>

    {{-- Settings Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">
                Affiliate System Configuration
            </h3>
            <p class="text-sm text-slate-500">
                Control global commissions and referral validity.
            </p>
        </div>

        <div class="p-6">
            {{-- form same rahega --}}
        </div>
    </div>

</div>

@endsection
