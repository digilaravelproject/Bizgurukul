@extends('layouts.admin')

@section('content')
<div x-data="paymentSettings()" x-cloak class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-mainText tracking-tight">Payment Gateways</h2>
            <p class="text-sm text-mutedText font-medium mt-1">Configure and manage your active payment integration.</p>
        </div>
        {{-- Top Save Button (Mobile Optimization) --}}
        <button type="submit" form="payment-settings-form" class="bg-primary text-white hover:bg-primary/90 px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 w-full md:w-auto">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </div>

    {{-- Success/Error Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 flex items-center gap-3 text-sm font-bold shadow-sm">
            <i class="fas fa-check-circle text-green-500 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 flex items-center gap-3 text-sm font-bold shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i> {{ session('error') }}
        </div>
    @endif

    <form id="payment-settings-form" action="{{ route('admin.settings.payment.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- ACTIVE GATEWAY SELECTOR (TABS) --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-4 sm:p-6">
            <h3 class="text-sm font-black text-mutedText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-exchange-alt"></i> Select Active Gateway
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Razorpay Option --}}
                <label class="cursor-pointer group relative" @click="activeGateway = 'razorpay'">
                    <input type="radio" name="active_payment_gateway" value="razorpay" x-model="activeGateway" class="sr-only peer">
                    <div class="flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-50/40 border-primary/10 hover:border-blue-200 group-hover:shadow-sm">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-black transition-colors"
                             :class="activeGateway === 'razorpay' ? 'bg-blue-500 text-white shadow-md shadow-blue-200' : 'bg-blue-50 text-blue-500'">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-black text-mainText">Razorpay</h4>
                                <span x-show="activeGateway === 'razorpay'" x-transition class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[10px] font-black uppercase">
                                    <i class="fas fa-check"></i> Active
                                </span>
                            </div>
                            <p class="text-xs text-mutedText font-medium mt-0.5">India's most popular gateway</p>
                        </div>
                    </div>
                </label>

                {{-- Cashfree Option --}}
                <label class="cursor-pointer group relative" @click="activeGateway = 'cashfree'">
                    <input type="radio" name="active_payment_gateway" value="cashfree" x-model="activeGateway" class="sr-only peer">
                    <div class="flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-300 peer-checked:border-purple-500 peer-checked:bg-purple-50/40 border-primary/10 hover:border-purple-200 group-hover:shadow-sm">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-black transition-colors"
                             :class="activeGateway === 'cashfree' ? 'bg-purple-500 text-white shadow-md shadow-purple-200' : 'bg-purple-50 text-purple-500'">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-black text-mainText">Cashfree</h4>
                                <span x-show="activeGateway === 'cashfree'" x-transition class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 text-[10px] font-black uppercase">
                                    <i class="fas fa-check"></i> Active
                                </span>
                            </div>
                            <p class="text-xs text-mutedText font-medium mt-0.5">Fast payouts & instant setup</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- DYNAMIC CONFIGURATION PANELS --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}

        {{-- 1. RAZORPAY SETTINGS --}}
        <div x-show="activeGateway === 'razorpay'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-surface rounded-2xl border border-blue-100 shadow-sm overflow-hidden" style="display: none;">

            {{-- Panel Header --}}
            <div class="bg-blue-50/50 border-b border-blue-100 px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm font-black text-blue-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-cogs text-blue-500"></i> Razorpay Configuration
                </h3>
                <button type="button" @click="testGateway('razorpay')"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wide bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 transition-all shadow-sm"
                        :disabled="testingRazorpay">
                    <i class="fas fa-plug" :class="testingRazorpay && 'animate-spin'"></i>
                    <span x-text="testingRazorpay ? 'Testing...' : 'Test Connection'"></span>
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-5">
                {{-- Connection Test Result --}}
                <div x-show="razorpayTestResult" x-transition class="p-3 rounded-lg text-xs font-bold"
                     :class="razorpayTestSuccess ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                    <i :class="razorpayTestSuccess ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                    <span x-text="razorpayTestResult" class="ml-1"></span>
                </div>

                {{-- API Credentials Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- API Key --}}
                    <div>
                        <label class="block text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">API Key (Key ID) <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-key text-blue-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                            <input type="text" name="razorpay_key" value="{{ old('razorpay_key', $settings['razorpay_key']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-4 py-2.5 text-sm font-semibold text-mainText focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none"
                                placeholder="rzp_live_xxxxxxxxxxxxx">
                        </div>
                        @error('razorpay_key') <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- API Secret --}}
                    <div>
                        <label class="block text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">API Secret <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-blue-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                            <input :type="showSecrets.razorpaySecret ? 'text' : 'password'" name="razorpay_secret" value="{{ old('razorpay_secret', $settings['razorpay_secret']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-10 py-2.5 text-sm font-semibold text-mainText focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none"
                                placeholder="••••••••••••••••">
                            <button type="button" @click="showSecrets.razorpaySecret = !showSecrets.razorpaySecret"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-mutedText hover:text-blue-600 transition-colors">
                                <i class="fas text-sm" :class="showSecrets.razorpaySecret ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="border-primary/5">

                {{-- Webhook Configuration --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                    {{-- Webhook Secret --}}
                    <div>
                        <label class="flex justify-between items-center text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">
                            <span>Webhook Secret</span>
                            <button type="button" @click="regenerateSecret('razorpay_webhook_secret')" class="text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                                <i class="fas fa-sync-alt"></i> Generate
                            </button>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-blue-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                            <input :type="showSecrets.razorpayWebhook ? 'text' : 'password'" name="razorpay_webhook_secret" id="razorpay_webhook_secret"
                                value="{{ old('razorpay_webhook_secret', $settings['razorpay_webhook_secret']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-10 py-2.5 text-sm font-semibold text-mainText focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none"
                                placeholder="••••••••••••••••">
                            <button type="button" @click="showSecrets.razorpayWebhook = !showSecrets.razorpayWebhook"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-mutedText hover:text-blue-600 transition-colors">
                                <i class="fas text-sm" :class="showSecrets.razorpayWebhook ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Webhook URL --}}
                    <div>
                        <label class="flex justify-between items-center text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">
                            <span>Webhook URL Setup</span>
                            <button type="button" @click="testWebhook('razorpay')" class="text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1" :disabled="testingRazorpayWebhook">
                                <i class="fas fa-vial" :class="testingRazorpayWebhook && 'animate-spin'"></i> Test
                            </button>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $webhookUrls['razorpay'] }}" readonly id="razorpay_webhook_url"
                                class="w-full bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5 text-xs font-mono text-blue-800 select-all outline-none">
                            <button type="button" onclick="copyToClipboard('razorpay_webhook_url')"
                                    class="shrink-0 w-10 h-[42px] flex items-center justify-center bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition-colors shadow-sm">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Webhook Test Result --}}
                <div x-show="razorpayWebhookResult" x-transition class="p-2.5 rounded-lg text-[11px] font-bold border"
                     :class="razorpayWebhookSuccess ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'">
                    <i :class="razorpayWebhookSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
                    <span x-text="razorpayWebhookResult" class="ml-1"></span>
                </div>
            </div>
        </div>

        {{-- 2. CASHFREE SETTINGS --}}
        <div x-show="activeGateway === 'cashfree'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-surface rounded-2xl border border-purple-100 shadow-sm overflow-hidden" style="display: none;">

            {{-- Panel Header --}}
            <div class="bg-purple-50/50 border-b border-purple-100 px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm font-black text-purple-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-cogs text-purple-500"></i> Cashfree Configuration
                </h3>
                <div class="flex items-center gap-3">
                    {{-- Environment Toggle --}}
                    <div class="flex bg-white rounded-lg p-1 border border-purple-200 shadow-sm">
                        <label class="cursor-pointer">
                            <input type="radio" name="cashfree_environment" value="sandbox" x-model="cashfreeEnv" class="sr-only peer">
                            <div class="px-3 py-1 rounded-md text-[10px] font-bold uppercase transition-all peer-checked:bg-amber-100 peer-checked:text-amber-800 text-mutedText hover:text-mainText">
                                Sandbox
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="cashfree_environment" value="production" x-model="cashfreeEnv" class="sr-only peer">
                            <div class="px-3 py-1 rounded-md text-[10px] font-bold uppercase transition-all peer-checked:bg-green-100 peer-checked:text-green-800 text-mutedText hover:text-mainText">
                                Production
                            </div>
                        </label>
                    </div>

                    <button type="button" @click="testGateway('cashfree')"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wide bg-white border border-purple-200 text-purple-600 hover:bg-purple-50 transition-all shadow-sm"
                            :disabled="testingCashfree">
                        <i class="fas fa-plug" :class="testingCashfree && 'animate-spin'"></i>
                        <span class="hidden sm:inline" x-text="testingCashfree ? 'Testing...' : 'Test Connection'"></span>
                    </button>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-5">
                {{-- Env Notice --}}
                <div class="p-2.5 rounded-lg text-[11px] font-bold flex items-center gap-2"
                     :class="cashfreeEnv === 'production' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-amber-50 text-amber-800 border border-amber-200'">
                    <i class="fas fa-info-circle text-lg"></i>
                    <span x-text="cashfreeEnv === 'production' ? 'LIVE MODE: Real payments will be processed. Use production API keys.' : 'SANDBOX MODE: Test payments only. No real money will be charged.'"></span>
                </div>

                {{-- Connection Test Result --}}
                <div x-show="cashfreeTestResult" x-transition class="p-3 rounded-lg text-xs font-bold"
                     :class="cashfreeTestSuccess ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                    <i :class="cashfreeTestSuccess ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                    <span x-text="cashfreeTestResult" class="ml-1"></span>
                </div>

                {{-- API Credentials Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- App ID --}}
                    <div>
                        <label class="block text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">App ID <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-id-badge text-purple-400 group-focus-within:text-purple-600 transition-colors"></i>
                            </div>
                            <input type="text" name="cashfree_app_id" value="{{ old('cashfree_app_id', $settings['cashfree_app_id']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-4 py-2.5 text-sm font-semibold text-mainText focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                                placeholder="Enter App ID">
                        </div>
                        @error('cashfree_app_id') <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Secret Key --}}
                    <div>
                        <label class="block text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">Secret Key <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-purple-400 group-focus-within:text-purple-600 transition-colors"></i>
                            </div>
                            <input :type="showSecrets.cashfreeSecret ? 'text' : 'password'" name="cashfree_secret_key" value="{{ old('cashfree_secret_key', $settings['cashfree_secret_key']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-10 py-2.5 text-sm font-semibold text-mainText focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                                placeholder="••••••••••••••••">
                            <button type="button" @click="showSecrets.cashfreeSecret = !showSecrets.cashfreeSecret"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-mutedText hover:text-purple-600 transition-colors">
                                <i class="fas text-sm" :class="showSecrets.cashfreeSecret ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="border-primary/5">

                {{-- Webhook Configuration --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                    {{-- Webhook Secret --}}
                    <div>
                        <label class="flex justify-between items-center text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">
                            <span>Webhook Secret</span>
                            <button type="button" @click="regenerateSecret('cashfree_webhook_secret')" class="text-purple-600 hover:text-purple-800 transition-colors flex items-center gap-1">
                                <i class="fas fa-sync-alt"></i> Generate
                            </button>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-purple-400 group-focus-within:text-purple-600 transition-colors"></i>
                            </div>
                            <input :type="showSecrets.cashfreeWebhook ? 'text' : 'password'" name="cashfree_webhook_secret" id="cashfree_webhook_secret"
                                value="{{ old('cashfree_webhook_secret', $settings['cashfree_webhook_secret']) }}"
                                class="w-full bg-navy border border-primary/10 rounded-lg pl-10 pr-10 py-2.5 text-sm font-semibold text-mainText focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all outline-none"
                                placeholder="••••••••••••••••">
                            <button type="button" @click="showSecrets.cashfreeWebhook = !showSecrets.cashfreeWebhook"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-mutedText hover:text-purple-600 transition-colors">
                                <i class="fas text-sm" :class="showSecrets.cashfreeWebhook ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Webhook URL --}}
                    <div>
                        <label class="flex justify-between items-center text-[11px] font-bold text-mutedText uppercase tracking-wide mb-1.5">
                            <span>Webhook URL Setup</span>
                            <button type="button" @click="testWebhook('cashfree')" class="text-purple-600 hover:text-purple-800 transition-colors flex items-center gap-1" :disabled="testingCashfreeWebhook">
                                <i class="fas fa-vial" :class="testingCashfreeWebhook && 'animate-spin'"></i> Test
                            </button>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $webhookUrls['cashfree'] }}" readonly id="cashfree_webhook_url"
                                class="w-full bg-purple-50 border border-purple-100 rounded-lg px-3 py-2.5 text-xs font-mono text-purple-800 select-all outline-none">
                            <button type="button" onclick="copyToClipboard('cashfree_webhook_url')"
                                    class="shrink-0 w-10 h-[42px] flex items-center justify-center bg-purple-500 text-white rounded-lg text-sm hover:bg-purple-600 transition-colors shadow-sm">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Webhook Test Result --}}
                <div x-show="cashfreeWebhookResult" x-transition class="p-2.5 rounded-lg text-[11px] font-bold border"
                     :class="cashfreeWebhookSuccess ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'">
                    <i :class="cashfreeWebhookSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
                    <span x-text="cashfreeWebhookResult" class="ml-1"></span>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<style>
    /* Prevents flicker of Alpine data before loaded */
    [x-cloak] { display: none !important; }
</style>
<script>
    function paymentSettings() {
        return {
            activeGateway: '{{ $settings['active_payment_gateway'] }}',
            cashfreeEnv: '{{ $settings['cashfree_environment'] }}',

            // Visibility Toggles
            showSecrets: {
                razorpaySecret: false,
                razorpayWebhook: false,
                cashfreeSecret: false,
                cashfreeWebhook: false,
            },

            // Connection tests
            testingRazorpay: false,
            razorpayTestResult: '',
            razorpayTestSuccess: false,
            testingCashfree: false,
            cashfreeTestResult: '',
            cashfreeTestSuccess: false,

            // Webhook tests
            testingRazorpayWebhook: false,
            razorpayWebhookResult: '',
            razorpayWebhookSuccess: false,
            testingCashfreeWebhook: false,
            cashfreeWebhookResult: '',
            cashfreeWebhookSuccess: false,

            async testGateway(gateway) {
                const isRazor = gateway === 'razorpay';
                if (isRazor) {
                    this.testingRazorpay = true;
                    this.razorpayTestResult = '';
                } else {
                    this.testingCashfree = true;
                    this.cashfreeTestResult = '';
                }

                try {
                    const response = await fetch('{{ route("admin.settings.payment.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ gateway: gateway })
                    });

                    const data = await response.json();

                    if (isRazor) {
                        this.razorpayTestResult = data.message;
                        this.razorpayTestSuccess = data.success;
                        this.testingRazorpay = false;
                    } else {
                        this.cashfreeTestResult = data.message;
                        this.cashfreeTestSuccess = data.success;
                        this.testingCashfree = false;
                    }
                } catch (error) {
                    const msg = 'Network error. Please try again.';
                    if (isRazor) {
                        this.razorpayTestResult = msg;
                        this.razorpayTestSuccess = false;
                        this.testingRazorpay = false;
                    } else {
                        this.cashfreeTestResult = msg;
                        this.cashfreeTestSuccess = false;
                        this.testingCashfree = false;
                    }
                }
            },

            async testWebhook(gateway) {
                const isRazor = gateway === 'razorpay';
                if (isRazor) {
                    this.testingRazorpayWebhook = true;
                    this.razorpayWebhookResult = '';
                } else {
                    this.testingCashfreeWebhook = true;
                    this.cashfreeWebhookResult = '';
                }

                const secretField = isRazor ? document.getElementById('razorpay_webhook_secret') : document.getElementById('cashfree_webhook_secret');

                try {
                    const response = await fetch('{{ route("admin.settings.payment.test_webhook") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            gateway: gateway,
                            webhook_secret: secretField ? secretField.value : null
                        })
                    });

                    const data = await response.json();

                    if (isRazor) {
                        this.razorpayWebhookResult = data.message;
                        this.razorpayWebhookSuccess = data.success;
                        this.testingRazorpayWebhook = false;
                    } else {
                        this.cashfreeWebhookResult = data.message;
                        this.cashfreeWebhookSuccess = data.success;
                        this.testingCashfreeWebhook = false;
                    }
                } catch (error) {
                    const msg = 'Test failed. Could not reach server.';
                    if (isRazor) {
                        this.razorpayWebhookResult = msg;
                        this.razorpayWebhookSuccess = false;
                        this.testingRazorpayWebhook = false;
                    } else {
                        this.cashfreeWebhookResult = msg;
                        this.cashfreeWebhookSuccess = false;
                        this.testingCashfreeWebhook = false;
                    }
                }
            },

            regenerateSecret(fieldId) {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
                let result = '';
                for (let i = 0; i < 32; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                const input = document.getElementById(fieldId);
                if (input) {
                    input.value = result;
                    if (fieldId === 'razorpay_webhook_secret') this.showSecrets.razorpayWebhook = true;
                    if (fieldId === 'cashfree_webhook_secret') this.showSecrets.cashfreeWebhook = true;
                }
            }
        };
    }

    function copyToClipboard(inputId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = input.nextElementSibling;
            if (btn) {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => { btn.innerHTML = originalHTML; }, 1500);
            }
        });
    }
</script>
@endpush
@endsection
