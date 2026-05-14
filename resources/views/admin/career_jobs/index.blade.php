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
@endsection
