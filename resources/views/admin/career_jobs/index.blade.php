@extends('layouts.admin')

@section('content')
<div x-data="{ activeJob: null, showModal: false }">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-mainText tracking-tight">Career <span class="text-primary">Jobs</span></h1>
        <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-bold">Manage your job board listings</p>
    </div>
    <a href="{{ route('admin.career-jobs.create') }}" class="brand-gradient text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-transform">
        Add New Job
    </a>
</div>

{{-- Dynamic Resource Links Management Card --}}
<div class="bg-customWhite p-6 rounded-2xl border border-primary/5 shadow-sm mb-8">
    <h3 class="font-extrabold text-sm text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
        <i class="fas fa-link text-primary text-base"></i>
        Helpful Career Resources Links
    </h3>
    <form action="{{ route('admin.career-jobs.update-settings') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 px-1">How to Build Your Resume URL</label>
                <div class="relative group">
                    <i class="fas fa-file-alt absolute left-4 top-1/2 -translate-y-1/2 text-mutedText/40 text-xs transition-colors group-focus-within:text-primary"></i>
                    <input type="url" name="career_how_to_build_resume_url" value="{{ \App\Models\Setting::get('career_how_to_build_resume_url', '') }}" 
                        class="w-full bg-white border border-gray-200 rounded-xl py-2.5 pl-11 pr-4 text-xs font-bold text-mainText placeholder-mutedText/40 focus:border-primary/40 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="https://youtube.com/... or any URL">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 px-1">How to Apply URL</label>
                <div class="relative group">
                    <i class="fas fa-play-circle absolute left-4 top-1/2 -translate-y-1/2 text-mutedText/40 text-xs transition-colors group-focus-within:text-primary"></i>
                    <input type="url" name="career_how_to_apply_url" value="{{ \App\Models\Setting::get('career_how_to_apply_url', '') }}" 
                        class="w-full bg-white border border-gray-200 rounded-xl py-2.5 pl-11 pr-4 text-xs font-bold text-mainText placeholder-mutedText/40 focus:border-primary/40 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="https://youtube.com/... or any URL">
                </div>
            </div>
            <div>
                <button type="submit" class="w-full brand-gradient text-white py-3 px-6 rounded-xl font-bold text-xs uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-transform shadow-lg shadow-primary/20">
                    Save Links
                </button>
            </div>
        </div>
    </form>
</div>

<div class="bg-customWhite rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText">Company</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText">Job Details</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText text-center">Apply Count</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText">Status</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($jobs as $job)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 p-1.5 flex items-center justify-center shadow-sm">
                                @if($job->company_logo)
                                    <img src="{{ asset('storage/' . $job->company_logo) }}" class="w-full h-full object-contain rounded-md">
                                @else
                                    <span class="text-primary font-black text-sm uppercase">{{ substr($job->company_name, 0, 1) }}</span>
                                @endif
                            </div>
                            <span class="font-bold text-sm text-mainText">{{ $job->company_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-sm text-mainText">{{ $job->title->name }}</div>
                        <div class="text-[10px] text-mutedText mt-0.5 uppercase tracking-wider font-bold">
                            {{ $job->location->name }} • {{ $job->experience->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-md bg-green-50 text-green-600 text-[11px] font-black border border-green-100">{{ $job->applies_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($job->is_active)
                            <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-600 text-[10px] font-black uppercase tracking-widest border border-green-500/20">Active</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-600 text-[10px] font-black uppercase tracking-widest border border-red-500/20">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="activeJob = @js($job); showModal = true" class="p-2.5 text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="View Analytics & Details">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <a href="{{ route('admin.career-jobs.edit', $job->id) }}" class="p-2.5 text-primary hover:bg-primary/10 rounded-xl transition-all" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.career-jobs.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 text-secondary hover:bg-secondary/10 rounded-xl transition-all" title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                <i class="fas fa-briefcase text-2xl"></i>
                            </div>
                            <p class="text-mutedText font-bold text-xs uppercase tracking-widest">No jobs found</p>
                            <p class="text-[10px] text-mutedText/60 mt-1 uppercase tracking-widest font-bold">Create your first job listing to get started!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jobs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/20">
            <span class="text-xs font-bold text-mutedText">
                Showing Page <span class="text-primary">{{ $jobs->currentPage() }}</span> of {{ $jobs->lastPage() }}
            </span>
            <div class="scale-90 origin-center sm:origin-right w-full sm:w-auto flex justify-center">
                {{ $jobs->appends(request()->except('jobs_page'))->links('pagination::simple-tailwind') }}
            </div>
        </div>
    @endif
</div>

{{-- VIEW JOB DETAIL & ANALYTICS MODAL --}}
<div x-show="showModal" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

    <!-- Modal Content -->
    <div class="relative w-full max-w-2xl bg-surface rounded-[2rem] shadow-2xl border border-primary/10 overflow-hidden transform transition-all flex flex-col max-h-[85vh] z-10" 
         x-show="showModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-black text-mainText" x-text="activeJob ? activeJob.title.name : 'Job Details'"></h3>
                <p class="text-[10px] text-mutedText font-black uppercase tracking-widest mt-1" x-text="activeJob ? activeJob.company_name : ''"></p>
            </div>
            <button @click="showModal = false" class="text-mutedText hover:text-secondary transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
            <!-- Analytics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Views -->
                <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-4 text-center">
                    <div class="text-[9px] font-black uppercase text-blue-500 tracking-wider mb-1">Unique Views</div>
                    <div class="text-2xl font-black text-blue-600" x-text="activeJob ? activeJob.views_count : 0"></div>
                </div>
                <!-- Applies -->
                <div class="bg-green-50/50 border border-green-100 rounded-2xl p-4 text-center">
                    <div class="text-[9px] font-black uppercase text-green-500 tracking-wider mb-1">Unique Applies (Clicks)</div>
                    <div class="text-2xl font-black text-green-600" x-text="activeJob ? activeJob.applies_count : 0"></div>
                </div>
                <!-- Conversion Rate -->
                <div class="bg-purple-50/50 border border-purple-100 rounded-2xl p-4 text-center">
                    <div class="text-[9px] font-black uppercase text-purple-500 tracking-wider mb-1">Conversion Rate</div>
                    <div class="text-2xl font-black text-purple-600" x-text="activeJob && activeJob.views_count > 0 ? Math.round((activeJob.applies_count / activeJob.views_count) * 1000) / 10 + '%' : '0%'"></div>
                </div>
            </div>

            <!-- Job Specifications -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50/50 border border-gray-100 rounded-2xl p-4 text-[11px] font-black text-mainText">
                <div>
                    <span class="block text-[8px] text-mutedText/50 uppercase tracking-widest mb-1">Location</span>
                    <span x-text="activeJob ? activeJob.location.name : 'N/A'"></span>
                </div>
                <div>
                    <span class="block text-[8px] text-mutedText/50 uppercase tracking-widest mb-1">Experience</span>
                    <span x-text="activeJob ? activeJob.experience.name : 'N/A'"></span>
                </div>
                <div>
                    <span class="block text-[8px] text-mutedText/50 uppercase tracking-widest mb-1">Salary</span>
                    <span x-text="activeJob && activeJob.salary ? activeJob.salary.name : 'Negotiable'"></span>
                </div>
                <div>
                    <span class="block text-[8px] text-mutedText/50 uppercase tracking-widest mb-1">Posted On</span>
                    <span x-text="activeJob ? activeJob.posted_on : 'N/A'"></span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h4 class="text-[10px] font-black text-mainText uppercase tracking-widest mb-3 border-l-4 border-primary pl-2">Job Description</h4>
                <div class="prose prose-sm max-w-none text-mutedText text-xs leading-relaxed font-semibold max-h-[25vh] overflow-y-auto pr-2" x-html="activeJob ? activeJob.description : ''"></div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
            <button @click="showModal = false" class="px-6 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-mainText hover:bg-gray-50 transition-all">
                Close
            </button>
            <a :href="activeJob ? '/admin/career-jobs/' + activeJob.id + '/edit' : '#'" class="brand-gradient text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-md hover:scale-105 transition-all">
                Edit Job
            </a>
        </div>
    </div>
</div>
</div>
@endsection
