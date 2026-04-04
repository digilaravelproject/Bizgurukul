@forelse ($orders as $order)
    <tr class="hover:bg-navy transition-colors group">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-sm font-bold text-mainText">{{ $order->created_at->format('d M Y') }}</span>
                <span class="text-xs text-mutedText">{{ $order->created_at->format('h:i A') }}</span>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-bold text-mainText">{{ $order->invoice_no }}</div>
            <div class="text-[10px] text-mutedText mt-1">Order ID: {{ $order->razorpay_order_id ?? $order->id }}</div>
            @if($order->razorpay_payment_id)
                <div class="text-[10px] text-mutedText mt-0.5">Pay ID: {{ $order->razorpay_payment_id }}</div>
            @endif
        </td>

        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-mutedText text-xs font-bold border border-white shrink-0">
                    {{ substr($order->user->name ?? '?', 0, 1) }}
                </div>
                <div>
                    <span class="text-sm font-bold text-mainText block">{{ $order->user->name ?? 'Unknown' }}</span>
                    <span class="text-xs text-mutedText block">{{ $order->user->email ?? '' }}</span>
                </div>
            </div>
        </td>

        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                @if($order->user && $order->user->referrer)
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[10px] font-black border border-primary/20 shrink-0">
                        {{ substr($order->user->referrer->name, 0, 1) }}
                    </div>
                    <div>
                        <span class="text-sm font-bold text-mainText block leading-tight">{{ $order->user->referrer->name }}</span>
                        <span class="text-[10px] text-mutedText block mt-0.5">{{ $order->user->referrer->referral_code ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-mutedText">
                        <span class="text-[10px] uppercase font-black tracking-widest bg-navy px-2 py-1 rounded">Direct</span>
                    </div>
                @endif
            </div>
        </td>

        <td class="px-6 py-4">
            @if ($order->bundle)
                <span class="text-sm font-medium text-mainText">
                    {{ $order->bundle->title }}
                </span>
                <span class="text-[10px] text-mutedText block">Bundle</span>
            @elseif($order->course)
                <span class="text-sm font-medium text-mainText">
                    {{ $order->course->title }}
                </span>
                <span class="text-[10px] text-mutedText block">Course</span>
            @elseif($order->paymentable)
                <span class="text-sm font-medium text-mainText">
                    {{ $order->paymentable->title ?? ($order->paymentable->name ?? 'Item') }}
                </span>
                <span class="text-[10px] text-mutedText block">{{ class_basename($order->paymentable_type) }}</span>
            @else
                <span class="text-sm text-mutedText italic">Product ID: {{ $order->paymentable_id }}</span>
            @endif
        </td>

        <td class="px-6 py-4 text-right">
            <span class="text-base font-black text-primary">
                ₹{{ number_format($order->amount, 2) }}
            </span>
        </td>

        <td class="px-6 py-4 text-center">
            @if($order->status === 'success' || $order->status === 'captured')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 border border-green-200">
                    <i class="fas fa-check-circle mr-1"></i> Success
                </span>
            @elseif($order->status === 'failed')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-700">
                    Failed
                </span>
            @elseif($order->status === 'pending')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-700 cursor-help" title="User started checkout but payment not confirmed by Razorpay.">
                    <i class="fas fa-clock mr-1"></i> Pending
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">
                    {{ ucfirst($order->status) }}
                </span>
            @endif
        </td>

        <td class="px-6 py-4 text-center">
            @if($order->status === 'success' || $order->status === 'captured')
                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] uppercase tracking-widest bg-primary/10 hover:bg-primary text-primary hover:text-white px-3 py-1.5 rounded-lg font-black transition-all shadow-sm">
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
                <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-mainText">No Orders Found</h3>
                <p class="text-sm text-mutedText">There are no orders matching your selected criteria.</p>
            </div>
        </td>
    </tr>
@endforelse
