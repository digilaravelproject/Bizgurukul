@extends('layouts.user.app')

@section('content')
    <div class="p-6 lg:p-10 relative">
        {{-- Aesthetic Background Accents --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/2 -left-24 w-72 h-72 bg-secondary/5 blur-[100px] rounded-full pointer-events-none"></div>

        <nav class="mb-10 relative z-10">
            <a href="{{ route('student.career_jobs.index') }}" 
                class="group inline-flex items-center gap-3 text-mutedText hover:text-primary transition-all font-black text-[10px] uppercase tracking-[0.3em]">
                <div class="h-10 w-10 rounded-xl bg-surface border border-primary/10 flex items-center justify-center premium-shadow group-hover:scale-110 transition-transform">
                    <i class="fas fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                </div>
                Return to Portal
            </a>
        </nav>

        <div class="relative z-10 max-w-6xl mx-auto">
            <div class="bg-surface rounded-[2.5rem] border border-primary/10 premium-shadow overflow-hidden">
                <!-- Header Section -->
                <div class="p-8 lg:p-12 border-b border-primary/5 bg-gradient-to-br from-primary/[0.02] to-transparent flex flex-col md:flex-row gap-10 items-center md:items-start relative">
                    {{-- Decorative Logo Watermark --}}
                    <div class="absolute top-1/2 right-12 -translate-y-1/2 opacity-[0.03] select-none pointer-events-none hidden lg:block">
                        <i class="fas fa-briefcase text-[12rem] -rotate-12"></i>
                    </div>

                    <div class="shrink-0 relative">
                        @if($job->company_logo)
                            <div class="w-40 h-40 rounded-3xl border border-primary/10 p-6 bg-white shadow-2xl flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company_name }}" class="max-w-full max-h-full object-contain">
                            </div>
                        @else
                            <div class="w-40 h-40 brand-gradient flex items-center justify-center rounded-3xl text-white font-black text-6xl shadow-2xl shadow-primary/20">
                                {{ substr($job->company_name, 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="absolute -bottom-3 -right-3 h-10 w-10 bg-white rounded-xl border border-primary/10 flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-primary"></i>
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left relative">
                        <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-6">
                            <span class="bg-primary/10 text-primary px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-primary/10">
                                <i class="fas fa-bolt mr-2 animate-pulse"></i>Active Hiring
                            </span>
                            <span class="bg-surface text-mutedText/60 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-primary/5">
                                Posted {{ $job->posted_on->diffForHumans() }}
                            </span>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-black text-mainText tracking-tight mb-4 leading-[1.1]">{{ $job->title->name }}</h1>
                        
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-x-6 gap-y-3">
                            <p class="text-xl font-bold text-primary flex items-center gap-3">
                                <i class="fas fa-building text-sm opacity-60"></i>
                                {{ $job->company_name }}
                            </p>
                            <span class="h-1.5 w-1.5 rounded-full bg-mutedText/20 hidden md:block"></span>
                            <p class="text-lg font-bold text-mutedText flex items-center gap-3">
                                <i class="fas fa-map-marker-alt text-sm opacity-60"></i>
                                {{ $job->location->name }}
                            </p>
                        </div>

                        <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-10">
                            <div class="flex items-center gap-3 text-xs font-black text-mainText bg-white px-5 py-3 rounded-2xl border border-primary/10 shadow-sm">
                                <div class="h-8 w-8 rounded-lg bg-primary/5 flex items-center justify-center text-primary">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] text-mutedText/50 uppercase tracking-widest leading-none mb-1">Experience</p>
                                    {{ $job->experience->name }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-xs font-black text-mainText bg-white px-5 py-3 rounded-2xl border border-primary/10 shadow-sm">
                                <div class="h-8 w-8 rounded-lg bg-primary/5 flex items-center justify-center text-primary">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] text-mutedText/50 uppercase tracking-widest leading-none mb-1">Compensation</p>
                                    {{ $job->salary->name ?? 'Negotiable' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-8 lg:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                        <!-- Main Content -->
                        <div class="lg:col-span-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-px flex-1 bg-gradient-to-r from-primary/20 to-transparent"></div>
                                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.4em]">Job Specifications</h3>
                                <div class="h-px flex-1 bg-gradient-to-l from-primary/20 to-transparent"></div>
                            </div>
                            
                            <div class="prose prose-primary max-w-none prose-headings:font-black prose-headings:text-mainText prose-p:text-mutedText prose-p:font-medium prose-p:leading-relaxed prose-li:text-mutedText prose-li:font-medium">
                                {!! $job->description !!}
                            </div>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="lg:col-span-4 space-y-10">
                            {{-- Skills Section --}}
                            <div class="bg-navy/5 p-10 rounded-[2.5rem] border border-primary/5 relative overflow-hidden">
                                <div class="absolute -top-10 -right-10 h-32 w-32 bg-primary/5 rounded-full blur-3xl"></div>
                                
                                <h3 class="text-[10px] font-black text-mainText uppercase tracking-[0.3em] mb-8 flex items-center gap-4">
                                    <span class="h-1 w-6 bg-primary rounded-full"></span>
                                    Core Competencies
                                </h3>
                                
                                <div class="flex flex-wrap gap-3">
                                    @foreach($job->skills as $skill)
                                        <span class="bg-white text-mainText border border-primary/10 px-5 py-3 rounded-2xl text-[11px] font-black shadow-sm flex items-center gap-3 hover:border-primary/50 hover:-translate-y-1 transition-all cursor-default group">
                                            <i class="fas fa-check-circle text-primary opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Apply Action Card --}}
                            <div class="bg-surface p-10 rounded-[2.5rem] border border-primary/10 text-center relative overflow-hidden group premium-shadow">
                                <div class="absolute inset-0 bg-primary opacity-0 group-hover:opacity-[0.02] transition-opacity"></div>
                                
                                <div class="h-20 w-20 brand-gradient rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-primary/30 rotate-3 group-hover:rotate-0 transition-transform">
                                    <i class="fas fa-paper-plane text-white text-2xl"></i>
                                </div>
                                
                                <h3 class="font-black text-mainText text-2xl mb-3">Begin Application</h3>
                                <p class="text-sm text-mutedText font-medium mb-10 leading-relaxed px-4">
                                    Taking the next step is easy. You'll be redirected to the secure portal at <b>{{ $job->company_name }}</b>.
                                </p>
                                
                                <a href="{{ $job->apply_link }}" target="_blank"
                                    class="block w-full brand-gradient text-white text-center font-black py-5 px-8 rounded-2xl hover:scale-[1.03] active:scale-95 transition-all shadow-2xl shadow-primary/30 text-xs uppercase tracking-widest">
                                    Apply Now
                                </a>
                                
                                <div class="mt-8 pt-8 border-t border-primary/5">
                                    <p class="text-[10px] font-bold text-mutedText/40 uppercase tracking-widest flex items-center justify-center gap-2">
                                        <i class="fas fa-shield-alt"></i>
                                        Verified Opportunity
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection