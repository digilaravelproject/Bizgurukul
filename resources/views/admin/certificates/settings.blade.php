@extends('layouts.admin')
@section('title', 'Certificate Settings')

@section('content')
<div class="space-y-8 font-sans text-mainText">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Certificate <span class="text-primary">Settings</span></h1>
            <p class="text-mutedText mt-1 text-sm">Upload and manage your certificate background template.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-mutedText">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-primary">Certificate Settings</span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center gap-3 font-bold text-sm animate-fade-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-center gap-3 font-bold text-sm animate-fade-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Left: Upload Form --}}
        <div class="bg-surface rounded-3xl p-8 shadow-sm border border-primary/10">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2.5 bg-primary/10 rounded-xl text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-mainText">Upload Template</h3>
            </div>

            <form action="{{ route('admin.certificate.settings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-3" for="certificate_template">
                        Template Image (JPEG, PNG, WEBP)
                    </label>
                    <div class="relative group">
                        <input class="block w-full text-sm text-mutedText
                            file:mr-4 file:py-3 file:px-6
                            file:rounded-xl file:border-0
                            file:text-xs file:font-bold file:uppercase file:tracking-widest
                            file:bg-navy file:text-primary
                            hover:file:bg-primary hover:file:text-white
                            file:transition-all file:cursor-pointer
                            bg-navy/30 rounded-2xl border border-primary/10 focus:border-primary/30 outline-none
                            cursor-pointer transition-all"
                            type="file" id="certificate_template" name="certificate_template" accept="image/*" required>
                    </div>
                    @error('certificate_template')
                        <p class="text-xs font-bold text-red-500 mt-2">{{ $message }}</p>
                    @enderror

                    <div class="mt-4 p-4 rounded-xl bg-primary/5 border border-primary/10 flex gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-mutedText leading-relaxed font-medium">
                            We recommend uploading an <span class="text-mainText font-bold">A4 landscape image</span> (e.g. 1123 x 794 px). Ensure you leave enough empty space in the center for dynamic text like student names and course titles.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-4 brand-gradient text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-lg shadow-primary/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 active:scale-95">
                        Save Template
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Preview --}}
        <div class="bg-surface rounded-3xl p-8 shadow-sm border border-primary/10">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2.5 bg-secondary/10 rounded-xl text-secondary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-mainText">Template Preview</h3>
            </div>

            <div class="bg-navy/50 rounded-2xl p-4 border border-primary/5 min-h-[300px] flex items-center justify-center relative overflow-hidden group">
                @if($templateUrl)
                    <img src="{{ $templateUrl }}" alt="Certificate Template" class="max-w-full h-auto rounded-lg border border-primary/10 shadow-2xl relative z-10">
                    <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm z-20">
                         <span class="px-4 py-2 bg-white text-primary rounded-full font-bold text-xs shadow-xl scale-75 group-hover:scale-100 transition-transform">Active Template</span>
                    </div>
                @else
                    <div class="text-center p-8">
                        <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-4 border border-primary/10">
                            <svg class="w-8 h-8 text-mutedText/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-mutedText">No template uploaded yet.</p>
                        <p class="text-[10px] uppercase font-bold text-mutedText/50 mt-1 tracking-widest">Awaiting configuration</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

