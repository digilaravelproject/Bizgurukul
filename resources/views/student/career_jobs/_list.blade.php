@if($jobs->isEmpty())
    <div class="bg-surface p-16 rounded-[3rem] border border-primary/10 border-dashed text-center premium-shadow">
        <div class="h-24 w-24 bg-primary/5 rounded-[2rem] flex items-center justify-center mx-auto mb-8 rotate-12 group-hover:rotate-0 transition-transform">
            <i class="fas fa-search text-4xl text-primary/20"></i>
        </div>
        <h3 class="text-2xl font-black text-mainText mb-3 uppercase tracking-widest">No results found</h3>
        <p class="text-mutedText text-sm font-medium max-w-sm mx-auto leading-relaxed">We couldn't find any opportunities matching your current filters. Try broadening your search criteria.</p>
        <button @click="resetFilters()" class="mt-8 text-primary font-black text-[10px] uppercase tracking-[0.3em] hover:text-secondary transition-colors underline decoration-2 underline-offset-8">Clear all filters</button>
    </div>
@else
    @foreach($jobs as $job)
        <div class="group bg-surface p-6 md:p-8 rounded-[2.5rem] border border-primary/10 hover:border-primary/30 premium-shadow hover-lift transition-all duration-500 flex flex-col md:flex-row gap-8 justify-between items-start md:items-center relative overflow-hidden">
            {{-- Decorative Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

            <div class="flex gap-6 items-start relative z-10">
                <div class="shrink-0">
                    @if($job->company_logo)
                        <div class="w-20 h-20 rounded-2xl border border-primary/10 p-3 bg-white shadow-sm flex items-center justify-center overflow-hidden group-hover:scale-110 transition-transform duration-500">
                            <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company_name }}" class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div class="w-20 h-20 brand-gradient flex items-center justify-center rounded-2xl text-white font-black text-3xl shadow-xl shadow-primary/20 group-hover:rotate-6 transition-transform">
                            {{ substr($job->company_name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-[9px] font-black text-primary uppercase tracking-[0.2em] bg-primary/10 px-2.5 py-1 rounded-lg border border-primary/10">Active Hiring</span>
                        <span class="text-[9px] font-black text-mutedText/40 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="far fa-clock"></i>
                            {{ $job->posted_on->diffForHumans() }}
                        </span>
                    </div>
                    
                    <h2 class="text-2xl font-black text-mainText group-hover:text-primary transition-colors leading-tight truncate">
                        <a href="{{ route('student.career_jobs.show', $job->id) }}">{{ $job->title->name ?? 'Job Opportunity' }}</a>
                    </h2>
                    
                    <p class="font-bold text-mutedText text-sm flex items-center gap-2 mt-1 uppercase tracking-wider">
                        <i class="fas fa-building text-[10px] text-primary/40"></i>
                        {{ $job->company_name }}
                    </p>
                    
                    <div class="flex flex-wrap gap-3 mt-5">
                        <div class="flex items-center gap-2 text-[10px] font-black text-mutedText bg-navy/5 px-4 py-2 rounded-xl border border-primary/5 uppercase tracking-widest">
                            <i class="fas fa-map-marker-alt text-primary/40"></i>
                            {{ $job->location->name ?? 'Remote' }}
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black text-mutedText bg-navy/5 px-4 py-2 rounded-xl border border-primary/5 uppercase tracking-widest">
                            <i class="fas fa-briefcase text-primary/40"></i>
                            {{ $job->experience->name ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black text-mutedText bg-navy/5 px-4 py-2 rounded-xl border border-primary/5 uppercase tracking-widest">
                            <i class="fas fa-wallet text-primary/40"></i>
                            {{ $job->salary->name ?? 'Undisclosed' }}
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($job->skills->take(3) as $skill)
                            <span class="bg-surface text-mainText text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-primary/10 shadow-sm">{{ $skill->name }}</span>
                        @endforeach
                        @if($job->skills->count() > 3)
                            <span class="text-[9px] font-black text-mutedText/40 uppercase tracking-widest self-center ml-2">+{{ $job->skills->count() - 3 }} Skills</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-full md:w-auto shrink-0 flex flex-col items-stretch md:items-end gap-4 border-t md:border-t-0 border-primary/5 pt-6 md:pt-0 relative z-10">
                <a href="{{ route('student.career_jobs.show', $job->id) }}" class="brand-gradient text-white px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 active:scale-95 transition-all text-center">
                    Explore Role
                </a>
                <p class="text-[9px] font-black text-mutedText/30 uppercase tracking-[0.3em] text-center md:text-right px-2">ID:SKP-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
    @endforeach
@endif

