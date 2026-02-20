@extends('layouts.user.app')
@section('title', 'My Invoices')

@section('content')
<div class="space-y-8 font-sans text-mainText pb-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
        <div>
            <div class="flex items-center gap-3 mb-2">
                 <a href="{{ route('student.dashboard') }}" class="p-2 rounded-xl bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-black tracking-tight text-mainText">My Invoices</h1>
            </div>
            <p class="text-mutedText text-base font-medium ml-12">History of your purchases and payments.</p>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-surface rounded-[2.5rem] border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden animate-fade-in-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-xs uppercase text-mutedText font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Order ID</th>
                        <th class="px-8 py-5">Item</th>
                        <th class="px-8 py-5">Amount</th>
                        <th class="px-8 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-8 py-5 text-sm font-semibold text-mutedText">
                            {{ $invoice->created_at->format('d M Y') }}
                        </td>
                        <td class="px-8 py-5 text-sm font-bold text-mainText font-mono">
                            #{{ $invoice->razorpay_order_id ?? $invoice->id }}
                        </td>
                        <td class="px-8 py-5">
                            @if($invoice->bundle)
                                <span class="text-sm font-bold text-mainText">{{ $invoice->bundle->title }}</span>
                                <span class="block text-[10px] text-mutedText uppercase tracking-wider">Bundle</span>
                            @elseif($invoice->course)
                                <span class="text-sm font-bold text-mainText">{{ $invoice->course->title }}</span>
                                <span class="block text-[10px] text-mutedText uppercase tracking-wider">Course</span>
                            @elseif($invoice->paymentable)
                                <span class="text-sm font-bold text-mainText">{{ $invoice->paymentable->title ?? $invoice->paymentable->name ?? 'Item' }}</span>
                                <span class="block text-[10px] text-mutedText uppercase tracking-wider">{{ class_basename($invoice->paymentable_type) }}</span>
                            @else
                                <span class="text-sm font-bold text-mainText">Unknown Item</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-sm font-black text-mainText">
                            ₹{{ number_format($invoice->amount) }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('student.invoices.show', $invoice->id) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl brand-gradient text-customWhite text-xs font-bold uppercase tracking-widest shadow-lg hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-file-invoice"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-file-invoice-dollar text-3xl text-primary/40"></i>
                                </div>
                                <h4 class="text-xl font-bold text-mainText">No Invoices Found</h4>
                                <p class="text-sm text-mutedText mt-2">You haven't made any purchases yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="p-6 border-t border-primary/10">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
