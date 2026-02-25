@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-black text-mainText tracking-tight">Email Configuration</h2>
    <p class="text-sm text-mutedText font-medium mt-1">Configure SMTP settings for outgoing emails and manage admin notifications.</p>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-3.5 rounded-xl text-sm font-bold">
        <i class="fas fa-check-circle text-green-400"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-5 py-3.5 rounded-xl text-sm font-bold">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="max-w-4xl xl:max-w-5xl space-y-6">

    {{-- SMTP Configuration Card --}}
    <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>

        <form action="{{ route('admin.settings.email.update') }}" method="POST" class="space-y-8 relative z-10">
            @csrf

            {{-- SMTP Section --}}
            <div>
                <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fas fa-server text-primary"></i> SMTP Configuration
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Host --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">SMTP Host <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                               placeholder="e.g. smtp.gmail.com or smtp.mailtrap.io" required
                               class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('mail_host') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Port --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Port <span class="text-red-500">*</span></label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                               placeholder="587" required
                               class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        <p class="text-[11px] text-mutedText mt-1.5"><i class="fas fa-info-circle text-primary/70 mr-1"></i> Common: 587 (TLS), 465 (SSL), 25 (None)</p>
                        @error('mail_port') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Encryption --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Encryption <span class="text-red-500">*</span></label>
                        <select name="mail_encryption" required
                                class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                            @foreach(['tls' => 'TLS (Recommended)', 'ssl' => 'SSL', 'none' => 'None'] as $val => $label)
                                <option value="{{ $val }}" {{ old('mail_encryption', $settings['mail_encryption']) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('mail_encryption') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Email ID (Username) <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}"
                               placeholder="youremail@gmail.com" required
                               class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('mail_username') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="mail_password" id="mail_password"
                                   placeholder="{{ $settings['mail_password'] ? '••••••••••• (saved)' : 'Enter SMTP password' }}"
                                   class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 pr-12 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-mutedText hover:text-primary transition-colors">
                                <i class="fas fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-mutedText mt-1.5"><i class="fas fa-lock text-primary/70 mr-1"></i> Leave blank to keep existing password.</p>
                        @error('mail_password') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            <hr class="border-primary/5">

            {{-- Sender Info --}}
            <div>
                <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-primary"></i> Sender Identity
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Sender Name <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                               placeholder="e.g. Skills Pehle" required
                               class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('mail_from_name') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Sender Email <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                               placeholder="noreply@skillspehle.com" required
                               class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('mail_from_address') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-primary/5">

            {{-- Admin Notification Email --}}
            <div>
                <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fas fa-bell text-primary"></i> Admin Notifications
                </h3>
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Admin Notification Email <span class="text-red-500">*</span></label>
                    <input type="email" name="admin_notification_email" value="{{ old('admin_notification_email', $settings['admin_notification_email']) }}"
                           placeholder="admin@skillspehle.com" required
                           class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    <p class="text-[11px] text-mutedText mt-1.5"><i class="fas fa-info-circle text-primary/70 mr-1"></i> Admin will receive new registration, purchase, withdrawal, and coupon transfer alerts at this address.</p>
                    @error('admin_notification_email') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-primary/10">
                <button type="submit" class="bg-primary text-white hover:bg-primary/90 px-8 py-3.5 rounded-xl font-bold uppercase tracking-widest text-sm transition-all shadow-lg flex items-center gap-3">
                    <i class="fas fa-save"></i> Save Email Settings
                </button>
            </div>

        </form>
    </div>

    {{-- Test Email Card --}}
    <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 relative overflow-hidden">
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-green-500/5 blur-[80px] rounded-full pointer-events-none"></div>

        <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-2 flex items-center gap-2 relative z-10">
            <i class="fas fa-flask text-green-400"></i> Test Email Configuration
        </h3>
        <p class="text-sm text-mutedText mb-5 relative z-10">Send a test email to verify your SMTP configuration is working correctly.</p>

        <div class="flex flex-col sm:flex-row gap-3 relative z-10">
            <input type="email" id="test_email_input" placeholder="Enter recipient email to test..."
                   class="flex-1 bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
            <button onclick="sendTestEmail()" id="test_btn"
                    class="bg-green-600 hover:bg-green-500 text-white px-7 py-3 rounded-xl font-bold uppercase tracking-widest text-sm transition-all shadow-lg flex items-center justify-center gap-2 min-w-[160px]">
                <i class="fas fa-paper-plane"></i> Send Test
            </button>
        </div>
        <div id="test_result" class="mt-4 hidden"></div>
    </div>

</div>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('mail_password');
    const icon  = document.getElementById('toggle-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

async function sendTestEmail() {
    const email  = document.getElementById('test_email_input').value.trim();
    const btn    = document.getElementById('test_btn');
    const result = document.getElementById('test_result');

    if (!email) {
        result.innerHTML = '<p class="text-yellow-400 text-sm font-bold flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please enter a recipient email address.</p>';
        result.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    result.classList.add('hidden');

    try {
        const resp = await fetch('{{ route("admin.settings.email.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ test_email: email })
        });
        const data = await resp.json();

        if (data.success) {
            result.innerHTML = `<p class="text-green-400 text-sm font-bold flex items-center gap-2"><i class="fas fa-check-circle"></i> ${data.message}</p>`;
        } else {
            result.innerHTML = `<p class="text-red-400 text-sm font-bold flex items-center gap-2"><i class="fas fa-times-circle"></i> ${data.message}</p>`;
        }
    } catch(e) {
        result.innerHTML = '<p class="text-red-400 text-sm font-bold flex items-center gap-2"><i class="fas fa-times-circle"></i> An unexpected error occurred.</p>';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Test';
        result.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
