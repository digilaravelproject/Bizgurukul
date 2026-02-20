<div class="bg-surface rounded-[2.5rem] border border-primary/10 shadow-sm overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full w-full">
    {{-- Thumbnail --}}
    <div class="relative h-56 overflow-hidden bg-navy/5">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
        @else
             <div class="w-full h-full flex items-center justify-center brand-gradient text-white/20 text-4xl font-black">
                {{ substr($course->title, 0, 2) }}
            </div>
        @endif

        <div class="absolute top-4 right-4">
            <span class="bg-green-500 text-white text-[9px] font-black uppercase px-3 py-1 rounded-full shadow-lg tracking-widest">
                <i class="fas fa-check-circle mr-1"></i> Owned
            </span>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex-grow flex flex-col justify-between">
        <div class="space-y-3">
            <h3 class="text-lg font-black text-mainText leading-tight tracking-tight line-clamp-2 group-hover:text-primary transition-colors">
                {{ $course->title }}
            </h3>
            <p class="text-xs text-mutedText font-medium leading-relaxed line-clamp-3">
                {{ $course->description }}
            </p>
        </div>

        {{-- Footer Button --}}
        <div class="pt-6 border-t border-primary/5 mt-6">
            <a href="{{ route('student.watch', $course->id) }}"
               class="w-full flex items-center justify-center gap-2 bg-navy text-primary border border-primary/20 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-lg active:scale-95 group-hover:shadow-primary/20">
                <i class="fas fa-play"></i> Start Learning
            </a>
        </div>
    </div>
</div>
