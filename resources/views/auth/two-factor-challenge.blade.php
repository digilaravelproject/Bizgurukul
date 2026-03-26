<x-guest-layout>
<div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-navy relative overflow-hidden">
    {{-- Animated Background Blobs --}}
    <div class="absolute top-0 -left-4 w-72 h-72 bg-primary/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-secondary/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-emerald-500/10 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-surface/80 backdrop-blur-xl border border-primary/10 shadow-2xl overflow-hidden sm:rounded-3xl relative z-10">
        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shield-alt text-3xl text-primary"></i>
            </div>
            <h2 class="text-2xl font-black text-mainText tracking-tight italic">Secure <span class="text-primary">Verification</span></h2>
            <p class="text-[10px] font-black uppercase text-mutedText tracking-[0.2em] mt-2">Enter your 6-digit authenticator code</p>
        </div>

        <form method="POST" action="{{ route('two-factor.challenge.store') }}">
            @csrf

            <div>
                <label for="code" class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-3 ml-1">Verification Code</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary text-mutedText/50">
                        <i class="fas fa-key text-sm"></i>
                    </div>
                    <input id="code" type="text" name="code" required autofocus maxlength="6" placeholder="000000"
                           class="block w-full pl-11 pr-4 py-4 bg-navy/50 border border-primary/10 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition text-mainText font-black text-center text-xl tracking-[0.5em] placeholder-mutedText/20">
                </div>
                @error('code')
                    <p class="mt-2 text-[10px] font-black text-secondary tracking-widest uppercase ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-xl text-xs font-black uppercase tracking-[0.2em] text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all transform hover:-translate-y-1 active:translate-y-0">
                    Verify Code
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <p class="text-[9px] font-black text-mutedText uppercase tracking-widest">
                Wait for a new code in your authenticator app
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    .animation-delay-4000 { animation-delay: 4s; }
</style>
</x-guest-layout>
