@extends('layouts.admin')

@section('content')
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
                    <td class="px-6 py-4">
                        @if($job->is_active)
                            <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-600 text-[10px] font-black uppercase tracking-widest border border-green-500/20">Active</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-600 text-[10px] font-black uppercase tracking-widest border border-red-500/20">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
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
                    <td colspan="4" class="px-6 py-16 text-center">
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
</div>

<div class="bg-customWhite rounded-2xl border border-primary/5 shadow-sm overflow-hidden mt-8">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h3 class="font-extrabold text-sm text-mainText uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-chart-bar text-primary"></i>
            Apply Count & Views Analytics
        </h3>
        <p class="text-[10px] text-mutedText mt-1 uppercase font-bold">Unique student views and application clicks per job (Duplicates excluded)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-mutedText">Company</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-mutedText">Job Role</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-mutedText text-center">Unique Views</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-mutedText text-center">Unique Applies (Clicks)</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-mutedText text-center">Apply Conversion Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($jobs as $job)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 p-1 flex items-center justify-center shadow-sm">
                                @if($job->company_logo)
                                    <img src="{{ asset('storage/' . $job->company_logo) }}" class="w-full h-full object-contain rounded-md">
                                @else
                                    <span class="text-primary font-black text-xs uppercase">{{ substr($job->company_name, 0, 1) }}</span>
                                @endif
                            </div>
                            <span class="font-bold text-xs text-mainText">{{ $job->company_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-xs text-mainText">{{ $job->title->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-600 text-[11px] font-black border border-blue-100">{{ $job->views_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-md bg-green-50 text-green-600 text-[11px] font-black border border-green-100">{{ $job->applies_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $views = $job->views_count ?? 0;
                            $applies = $job->applies_count ?? 0;
                            $rate = $views > 0 ? round(($applies / $views) * 100, 1) : 0;
                        @endphp
                        <span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-600 text-[11px] font-black border border-purple-100">
                            {{ $rate }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-xs text-mutedText uppercase font-bold">
                        No job stats available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
