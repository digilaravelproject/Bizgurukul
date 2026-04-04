@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="p-2 text-mutedText/30 cursor-not-allowed bg-primary/5 rounded-xl border border-primary/5 shadow-inner">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </span>
        @else
            <a @click.prevent="goToPage('{{ $paginator->previousPageUrl() }}')" 
               href="{{ $paginator->previousPageUrl() }}" 
               class="p-2 text-mutedText hover:text-primary hover:bg-white transition-all bg-surface border border-primary/10 rounded-xl shadow-sm hover:shadow-md active:scale-95 group">
                <i class="fas fa-chevron-left text-[10px] group-hover:-translate-x-0.5 transition-transform"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden md:flex items-center gap-1 mx-1">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-7 h-7 flex items-center justify-center text-mutedText/40 text-[9px] font-black tracking-widest">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" 
                                  class="w-7 h-7 flex items-center justify-center bg-primary text-white text-[10px] font-black rounded-lg shadow-md shadow-primary/20 ring-2 ring-primary/5 z-10">
                                {{ $page }}
                            </span>
                        @else
                            <a @click.prevent="goToPage('{{ $url }}')" 
                               href="{{ $url }}" 
                               class="w-7 h-7 flex items-center justify-center bg-surface border border-primary/10 text-[10px] font-black text-mainText hover:text-primary hover:bg-white hover:border-primary/20 transition-all rounded-lg shadow-sm hover:shadow-md active:scale-95">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="md:hidden flex items-center gap-2 bg-surface px-2 py-1 rounded-lg border border-primary/10 shadow-sm mx-1">
            <span class="text-[9px] font-black text-primary">{{ $paginator->currentPage() }}</span>
            <span class="text-mutedText/30 text-[7px] font-black">/</span>
            <span class="text-[9px] font-black text-mutedText">{{ $paginator->lastPage() }}</span>
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a @click.prevent="goToPage('{{ $paginator->nextPageUrl() }}')" 
               href="{{ $paginator->nextPageUrl() }}" 
               class="p-1.5 text-mutedText hover:text-primary hover:bg-white transition-all bg-surface border border-primary/10 rounded-lg shadow-sm hover:shadow-md active:scale-95 group">
                <i class="fas fa-chevron-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        @else
            <span class="p-1.5 text-mutedText/30 cursor-not-allowed bg-primary/5 rounded-lg border border-primary/5 shadow-inner">
                <i class="fas fa-chevron-right text-[8px]"></i>
            </span>
        @endif
    </nav>
@endif
