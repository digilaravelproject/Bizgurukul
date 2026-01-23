<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Admin Control Panel') }}
            </h2>
            <div class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                System Time: {{ now()->format('d M, H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 transition-hover hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Students</div>
                        <span class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ number_format($totalStudents) }}</div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 transition-hover hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">Paid Commissions</div>
                        <span class="p-2 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-extrabold text-green-600 mt-2">
                        ₹{{ number_format($totalCommissionsPaid, 2) }}</div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 transition-hover hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">Pending Payouts</div>
                        <span class="p-2 bg-orange-50 rounded-lg text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-extrabold text-orange-500 mt-2">
                        ₹{{ number_format($pendingCommissions, 2) }}</div>
                </div>
            </div>

            {{-- 2. Settings Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Affiliate System Configuration</h3>
                        <p class="text-slate-500 text-sm">Control global commissions and referral validity.</p>
                    </div>
                    <div class="h-10 w-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div
                            class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-bold border border-green-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 text-sm font-bold border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" x-data="{ changed: false }">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- Referral Toggle --}}
                            <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-dashed border-slate-300">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" name="referral_system_active" class="sr-only peer"
                                            @change="changed = true"
                                            {{ $settings['referral_system_active'] == '1' ? 'checked' : '' }}>
                                        <div
                                            class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <span
                                            class="block text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition">Enable
                                            Affiliate System</span>
                                        <span class="text-xs text-slate-500">When disabled, new referrals won't be
                                            tracked or rewarded.</span>
                                    </div>
                                </label>
                            </div>

                            {{-- Commission Amount --}}
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Commission
                                    Amount (₹)</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">₹</span>
                                    <input type="number" name="referral_commission_amount" @input="changed = true"
                                        value="{{ old('referral_commission_amount', $settings['referral_commission_amount']) }}"
                                        class="pl-8 w-full bg-white border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold">
                                </div>
                                @error('referral_commission_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Cookie Expiry --}}
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Cookie
                                    Validity (Days)</label>
                                <div class="relative">
                                    <input type="number" name="referral_cookie_expiry_days" @input="changed = true"
                                        value="{{ old('referral_cookie_expiry_days', $settings['referral_cookie_expiry_days']) }}"
                                        class="w-full bg-white border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold">
                                    <span
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-bold">Days</span>
                                </div>
                                @error('referral_cookie_expiry_days')
                                    <p class="text-red-500 text-xs mt-1">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <button type="submit"
                                class="inline-flex items-center justify-center bg-indigo-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95 disabled:opacity-50"
                                :class="changed ? 'opacity-100' : 'opacity-80'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Navigation to Commissions --}}
            <div class="flex justify-end">
                <a href="{{ Route::has('admin.commissions.index') ? route('admin.commissions.index') : '#' }}"
                    class="inline-flex items-center text-sm font-bold {{ Route::has('admin.commissions.index') ? 'text-indigo-600 hover:text-indigo-800' : 'text-slate-400 cursor-not-allowed' }}">
                    Manage All Payouts {{ Route::has('admin.commissions.index') ? '' : '(Coming Soon)' }}
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
