@forelse ($orders as $order)
    <tr class="hover:bg-navy/40 transition-colors group border-b border-primary/5">
        {{-- Date & Time --}}
        <td class="px-3.5 py-3.5 whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-xs font-bold text-mainText">{{ $order->created_at->format('d M Y') }}</span>
                <span class="text-[11px] text-mutedText/80 font-medium">{{ $order->created_at->format('h:i A') }}</span>
            </div>
        </td>

        {{-- Invoice & ID --}}
        <td class="px-3.5 py-3.5 whitespace-nowrap">
            <div class="text-xs font-black text-mainText tracking-tight">{{ $order->invoice_no }}</div>
            <div class="text-[10px] text-mutedText/80 mt-0.5 max-w-[170px] truncate" title="Order ID: {{ $order->gateway_order_id ?? ($order->razorpay_order_id ?? $order->id) }}">
                ID: {{ $order->gateway_order_id ?? ($order->razorpay_order_id ?? $order->id) }}
            </div>
            <div class="text-[10px] text-primary/80 font-bold flex items-center gap-1 mt-0.5">
                <span class="capitalize">{{ $order->payment_gateway ?? 'razorpay' }}</span>
                @if($order->gateway_payment_id || $order->razorpay_payment_id)
                    <span class="text-mutedText/50">•</span>
                    <span class="max-w-[100px] truncate" title="{{ $order->gateway_payment_id ?? $order->razorpay_payment_id }}">{{ $order->gateway_payment_id ?? $order->razorpay_payment_id }}</span>
                @endif
            </div>
        </td>

        {{-- User --}}
        <td class="px-3.5 py-3.5">
            <div class="flex items-center gap-2.5 max-w-[200px]">
                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[11px] font-black border border-primary/20 shrink-0">
                    {{ strtoupper(substr($order->user->name ?? ($order->lead->name ?? '?'), 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold text-mainText block truncate" title="{{ $order->user->name ?? ($order->lead->name ?? 'Unknown') }}">
                        {{ $order->user->name ?? ($order->lead->name ?? 'Unknown') }}
                    </span>
                    <span class="text-[11px] text-mutedText/80 block truncate" title="{{ $order->user->email ?? ($order->lead->email ?? '') }}">
                        {{ $order->user->email ?? ($order->lead->email ?? '') }}
                    </span>
                </div>
            </div>
        </td>

        {{-- Sponsor --}}
        <td class="px-3.5 py-3.5">
            <div class="flex items-center gap-2.5 max-w-[180px]">
                @if($order->user && $order->user->referrer)
                    <div class="w-7 h-7 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500 text-[10px] font-black border border-amber-500/20 shrink-0">
                        {{ strtoupper(substr($order->user->referrer->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-bold text-mainText block truncate leading-tight" title="{{ $order->user->referrer->name }}">
                            {{ $order->user->referrer->name }}
                        </span>
                        <span class="text-[10px] font-bold text-amber-600/90 block mt-0.5 truncate">
                            {{ $order->user->referrer->referral_code ?? 'N/A' }}
                        </span>
                    </div>
                @else
                    <div class="flex items-center text-mutedText">
                        <span class="text-[10px] uppercase font-black tracking-wider bg-navy px-2 py-0.5 rounded border border-primary/10">Direct</span>
                    </div>
                @endif
            </div>
        </td>

        {{-- Product --}}
        <td class="px-3.5 py-3.5">
            <div class="max-w-[180px]">
                @if ($order->bundle)
                    <span class="text-xs font-bold text-mainText block truncate" title="{{ $order->bundle->title }}">
                        {{ $order->bundle->title }}
                    </span>
                    <span class="text-[10px] font-bold text-primary block">Bundle</span>
                @elseif($order->course)
                    <span class="text-xs font-bold text-mainText block truncate" title="{{ $order->course->title }}">
                        {{ $order->course->title }}
                    </span>
                    <span class="text-[10px] font-bold text-blue-500 block">Course</span>
                @elseif($order->paymentable)
                    <span class="text-xs font-bold text-mainText block truncate" title="{{ $order->paymentable->title ?? ($order->paymentable->name ?? 'Item') }}">
                        {{ $order->paymentable->title ?? ($order->paymentable->name ?? 'Item') }}
                    </span>
                    <span class="text-[10px] font-bold text-mutedText block">{{ class_basename($order->paymentable_type) }}</span>
                @else
                    <span class="text-xs text-mutedText italic">ID: {{ $order->paymentable_id }}</span>
                @endif
            </div>
        </td>

        {{-- Amount --}}
        <td class="px-3.5 py-3.5 text-right whitespace-nowrap">
            <span class="text-sm font-black text-primary">
                ₹{{ number_format($order->amount, 2) }}
            </span>
        </td>

        {{-- Status --}}
        <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
            @if($order->status === 'success' || $order->status === 'captured')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    <i class="fas fa-check-circle mr-1 text-[9px]"></i> Success
                </span>
            @elseif($order->status === 'failed')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200/60">
                    Failed
                </span>
            @elseif($order->status === 'pending')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/60 cursor-help" title="Checkout started but payment pending.">
                    <i class="fas fa-clock mr-1 text-[9px]"></i> Pending
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-600">
                    {{ ucfirst($order->status) }}
                </span>
            @endif
        </td>

        {{-- Action --}}
        <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
            @if($order->status === 'success' || $order->status === 'captured')
                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] uppercase tracking-wider bg-primary/10 hover:bg-primary text-primary hover:text-white px-2.5 py-1 rounded-lg font-black transition-all shadow-sm">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    View
                </a>
            @else
                <span class="text-xs text-mutedText">-</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-14 h-14 bg-navy rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-base font-bold text-mainText">No Orders Found</h3>
                <p class="text-xs text-mutedText">There are no orders matching your selected criteria.</p>
            </div>
        </td>
    </tr>
@endforelse
