@php
    $catName = strtolower($course->category->name ?? 'uncategorized');
    $imageTag = match(true) {
        str_contains($catName, 'design') => 'photo-1561070791-2526d30994b5',
        str_contains($catName, 'marketing') => 'photo-1460925895917-afdab827c52f',
        str_contains($catName, 'code') || str_contains($catName, 'dev') => 'photo-1498050108023-c5249f4df085',
        default => 'photo-1516321318423-f06f85e504b3'
    };
@endphp
<div x-show="activeTab === 'all' || activeTab === '{{ $catName }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 border border-gray-100 flex flex-col group hover:-translate-y-1">
    <div class="relative h-40 overflow-hidden bg-gray-100">
        <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/'.$imageTag.'?q=80&w=600&auto=format&fit=crop' }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-2 py-1 rounded text-[10px] font-black text-secondary shadow-sm uppercase tracking-wider">{{ $course->subCategory->name ?? 'All Levels' }}</div>
    </div>
    <div class="p-4 flex flex-col flex-grow relative">
        <div class="flex items-center gap-1 mb-2 text-[11px] text-mutedText font-bold bg-navy w-max px-2 py-0.5 rounded border border-gray-100">
            <span class="flex items-center gap-1 text-yellow-500"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>4.9</span>
        </div>
        <h3 class="text-base font-bold text-mainText mb-2 group-hover:text-primary transition-colors line-clamp-2 leading-tight">{{ $course->title }}</h3>
        <p class="text-xs text-mutedText mb-4 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($course->description), 80) }}</p>

        <div class="mt-auto pt-3 border-t border-gray-100">
            <a href="{{ route('course.show', $course->slug ?? $course->id) }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary/10 text-primary font-bold text-sm group-hover:bg-primary group-hover:text-white transition-all duration-300">
                View Course
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</div>
