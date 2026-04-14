@forelse($inquiries as $inquiry)
    <tr class="hover:bg-primary/5 transition-colors group">
        <td class="px-8 py-5">
            <span class="font-bold text-mainText">{{ $inquiry->created_at->format('d M, Y') }}</span>
            <div class="text-[10px] text-mutedText mt-0.5">{{ $inquiry->created_at->diffForHumans() }}</div>
        </td>
        <td class="px-8 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-navy/50 flex items-center justify-center text-primary font-black text-xs border border-primary/10">
                    {{ substr($inquiry->name, 0, 2) }}
                </div>
                <div>
                    <div class="font-bold text-mainText text-sm">{{ $inquiry->name }}</div>
                    <div class="text-xs text-mutedText">{{ $inquiry->email }}</div>
                </div>
            </div>
        </td>
        <td class="px-8 py-5">
            <div class="font-bold text-mainText capitalize">{{ str_replace('_', ' ', $inquiry->subject) }}</div>
            <div class="text-xs text-mutedText truncate max-w-xs">{{ Str::limit($inquiry->message, 50) }}</div>
        </td>
        <td class="px-8 py-5">
            @if($inquiry->is_replied)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                    Replied
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500 border border-amber-500/20">
                    New
                </span>
            @endif
        </td>
        <td class="px-8 py-5 text-right">
            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.contact-inquiries.show', $inquiry->id) }}" class="p-2.5 rounded-xl bg-white border border-primary/10 text-primary hover:bg-primary hover:text-white transition-all shadow-sm">
                    <i class="fas fa-eye text-xs"></i>
                </a>
                <form action="{{ route('admin.contact-inquiries.destroy', $inquiry->id) }}" method="POST" onsubmit="return confirm('Delete this inquiry?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2.5 rounded-xl bg-white border border-secondary/10 text-secondary hover:bg-secondary hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-8 py-20 text-center">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-navy/30 rounded-full flex items-center justify-center text-primary/30 mb-4 border border-primary/10">
                    <i class="fas fa-inbox text-2xl"></i>
                </div>
                <p class="text-mutedText font-bold">No inquiries found.</p>
            </div>
        </td>
    </tr>
@endforelse
