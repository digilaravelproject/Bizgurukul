@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-mainText tracking-tight">Two-Factor <span class="text-primary">Authentication</span></h2>
    <p class="text-sm text-mutedText font-medium mt-1">Enhance your account security by enabling Google Authenticator.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-surface rounded-3xl border border-primary/10 p-6 shadow-sm h-full">
            <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-shield-alt text-2xl text-primary"></i>
            </div>
            <h3 class="text-lg font-black text-mainText mb-2 tracking-tight">Why use 2FA?</h3>
            <p class="text-xs font-bold text-mutedText leading-relaxed mb-4 uppercase tracking-wider">
                Two-Factor Authentication adds an extra layer of protection. Even if someone knows your password, they won't be able to access your account without a unique code from your mobile device.
            </p>
            <ul class="space-y-3">
                <li class="flex items-center gap-3 text-[10px] font-black uppercase text-mainText">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Prevents Unauthorized Access
                </li>
                <li class="flex items-center gap-3 text-[10px] font-black uppercase text-mainText">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Protects Sensitive Data
                </li>
                <li class="flex items-center gap-3 text-[10px] font-black uppercase text-mainText">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    Secure Login Process
                </li>
            </ul>
        </div>
    </div>

    {{-- Setup Card --}}
    <div class="lg:col-span-2">
        <div class="bg-surface rounded-3xl border border-primary/10 overflow-hidden shadow-sm">
            <div class="p-8">
                @if($user->hasTwoFactorEnabled())
                    <div class="flex items-center gap-6 mb-8 p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                        <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-mainText tracking-tight">2FA is Enabled</h4>
                            <p class="text-xs font-bold text-mutedText uppercase tracking-[0.1em]">Your account is protected by Google Authenticator.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.2fa.disable') }}" method="POST">
                        @csrf
                        <div class="bg-red-500/5 border border-red-500/10 p-6 rounded-2xl">
                            <h5 class="text-sm font-black text-mainText mb-2 uppercase tracking-tight">Danger Zone</h5>
                            <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-4">
                                Disabling 2FA will reduce your account security. Are you sure you want to proceed?
                            </p>
                            <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-red-500/20">
                                Disable Two-Factor
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mb-8">
                        <h4 class="text-xl font-black text-mainText tracking-tight mb-2">Configure Authenticator App</h4>
                        <p class="text-xs font-bold text-mutedText uppercase tracking-[0.1em]">Follow the steps below to setup 2FA.</p>
                    </div>

                    <div class="space-y-8">
                        {{-- Step 1 --}}
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white text-xs font-black">1</div>
                            <div>
                                <h5 class="text-sm font-black text-mainText uppercase tracking-tight mb-2">Scan the QR Code</h5>
                                <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-4">
                                    Open your authenticator app (Google Authenticator, Microsoft Authenticator, etc.) and scan this QR code.
                                </p>
                                <div class="p-4 bg-white rounded-2xl border border-primary/5 inline-block shadow-inner">
                                    {!! $qrCodeSvg !!}
                                </div>
                                <div class="mt-4 p-3 bg-navy rounded-xl border border-primary/10">
                                    <p class="text-[9px] font-black text-mutedText uppercase tracking-[0.2em] mb-1">Manual Entry Secret</p>
                                    <code class="text-xs font-black text-primary break-all tracking-wider">{{ $secret }}</code>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white text-xs font-black">2</div>
                            <div class="flex-1 max-w-sm">
                                <h5 class="text-sm font-black text-mainText uppercase tracking-tight mb-2">Verify & Enable</h5>
                                <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-4">
                                    Enter the 6-digit verification code from your authenticator app to confirm setup.
                                </p>
                                
                                <form action="{{ route('admin.settings.2fa.enable') }}" method="POST">
                                    @csrf
                                    <div class="flex gap-3">
                                        <input type="text" name="code" maxlength="6" placeholder="000000" 
                                               class="flex-1 bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-black text-mainText placeholder-mutedText/30 focus:border-primary outline-none transition uppercase tracking-[0.5em] text-center">
                                        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-primary/20">
                                            Enable
                                        </button>
                                    </div>
                                    @error('code')
                                        <p class="mt-2 text-[10px] font-black text-secondary tracking-widest uppercase">{{ $message }}</p>
                                    @enderror
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
