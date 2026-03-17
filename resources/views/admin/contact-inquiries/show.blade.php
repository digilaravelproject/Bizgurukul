@extends('layouts.admin')
@section('title', 'View Inquiry')

@section('content')
<div class="container-fluid font-sans antialiased max-w-4xl">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.contact-inquiries.index') }}" class="inline-flex items-center text-xs font-black text-mutedText hover:text-primary mb-4 transition-colors uppercase tracking-widest gap-2">
                <i class="fas fa-arrow-left"></i> Back to Inquiries
            </a>
            <h2 class="text-3xl font-black text-mainText tracking-tight">Inquiry Details</h2>
            <p class="text-sm text-mutedText mt-1 font-medium">Message from {{ $inquiry->name }} sent on {{ $inquiry->created_at->format('d M, Y \a\t h:i A') }}</p>
        </div>

        <div class="flex items-center gap-3">
            @if(!$inquiry->is_replied)
                <form action="{{ route('admin.contact-inquiries.mark-replied', $inquiry->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="brand-gradient px-6 py-3 rounded-xl text-xs font-black text-white shadow-lg shadow-primary/25 hover:-translate-y-0.5 transition-all">
                        Mark as Replied
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-[2rem] border border-primary/10 shadow-xl p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-navy/50 flex items-center justify-center text-primary text-xl font-black border border-primary/10">
                        {{ substr($inquiry->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-mainText">{{ $inquiry->name }}</h3>
                        <p class="text-sm text-mutedText font-medium">{{ $inquiry->email }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black uppercase text-primary tracking-[0.2em] mb-2">Subject</p>
                        <p class="text-lg font-bold text-mainText capitalize">{{ str_replace('_', ' ', $inquiry->subject) }}</p>
                    </div>

                    <div class="pt-6 border-t border-primary/5">
                        <p class="text-[10px] font-black uppercase text-primary tracking-[0.2em] mb-4">Message Content</p>
                        <div class="bg-navy/30 rounded-2xl p-6 text-mainText leading-relaxed font-medium">
                            {!! nl2br(e($inquiry->message)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-surface rounded-3xl p-8 border border-primary/5">
                <h4 class="text-sm font-black text-mainText mb-4 uppercase tracking-widest">How to reply?</h4>
                <p class="text-sm text-mutedText leading-relaxed mb-6 font-medium">
                    You can reply to this inquiry by clicking the email address above. Once you've handled the customer's request, mark it as replied to keep your inbox organized.
                </p>
                <a href="mailto:{{ $inquiry->email }}?subject=Re: {{ str_replace('_', ' ', $inquiry->subject) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-primary/10 text-primary font-bold text-xs hover:bg-primary hover:text-white transition-all shadow-sm">
                    <i class="fas fa-reply"></i> Send Email Reply
                </a>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-[2rem] border border-primary/10 shadow-xl p-6">
                <h4 class="text-[10px] font-black uppercase text-primary tracking-[0.2em] mb-4">Inquiry Stats</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-mutedText">Status</span>
                        @if($inquiry->is_replied)
                            <span class="text-emerald-500 flex items-center gap-1"><i class="fas fa-check-circle"></i> Replied</span>
                        @else
                            <span class="text-amber-500 flex items-center gap-1"><i class="fas fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold pt-4 border-t border-primary/5">
                        <span class="text-mutedText">Sent Date</span>
                        <span class="text-mainText">{{ $inquiry->created_at->format('d M, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-mutedText">Sent Time</span>
                        <span class="text-mainText">{{ $inquiry->created_at->format('h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-secondary/5 rounded-[2rem] border border-secondary/10 p-6">
                <h4 class="text-[10px] font-black uppercase text-secondary tracking-[0.2em] mb-4">Dangerous Zone</h4>
                <p class="text-xs text-mutedText font-medium mb-4">This action cannot be undone. All data for this inquiry will be permanently lost.</p>
                <form action="{{ route('admin.contact-inquiries.destroy', $inquiry->id) }}" method="POST" onsubmit="return confirm('Permanently delete this inquiry?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3 rounded-xl bg-white border border-secondary/20 text-secondary font-bold text-xs hover:bg-secondary hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Inquiry
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
