<div class="bg-surface rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col h-full group">
    {{-- Compact Image --}}
    <div class="relative h-40 overflow-hidden bg-gray-100">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
             <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400 text-2xl font-bold">
                {{ substr($course->title, 0, 2) }}
            </div>
        @endif

        <div class="absolute top-2.5 right-2.5">
            <span class="bg-green-500/90 text-white text-[9px] font-bold uppercase px-2 py-1 rounded-full shadow-sm tracking-wide flex items-center gap-1">
                <i class="fas fa-check-circle text-[8px]"></i> Owned
            </span>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-5 flex-grow flex flex-col">
        <h3 class="text-base font-bold text-mainText leading-snug group-hover:text-primary transition-colors line-clamp-2">
            {{ $course->title }}
        </h3>
        <p class="text-xs text-mutedText mt-2 line-clamp-2 leading-relaxed">
            {{ strip_tags($course->description) }}
        </p>

        {{-- Footer Button --}}
        <div class="mt-auto pt-4 border-t border-gray-50 flex flex-col gap-2">
            <a href="{{ route('student.watch', $course->id) }}"
               class="w-full flex items-center justify-center gap-2 bg-primary text-white py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-secondary transition-colors shadow-sm active:scale-95">
                <i class="fas fa-play text-[8px]"></i> Start Learning
            </a>
        </div>
    </div>
</div>
