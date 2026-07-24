@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.beginner-guide') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-all shadow-sm">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <h1 class="text-2xl font-black text-mainText tracking-tight uppercase">Beginner Guide Analytics</h1>
                </div>
                <p class="text-sm text-mutedText font-medium opacity-70 italic ml-12">Track student views, watch progress, and 100% completion metrics</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.beginner-guide') }}" class="px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-cog"></i> Manage Videos
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-black">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-[10px] font-black text-mutedText uppercase tracking-widest">Active Learners</div>
                    <div class="text-2xl font-black text-mainText">{{ $uniqueUsers }}</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-black">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="text-[10px] font-black text-mutedText uppercase tracking-widest">Total Views Tracked</div>
                    <div class="text-2xl font-black text-mainText">{{ $totalViews }}</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-black">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-[10px] font-black text-mutedText uppercase tracking-widest">100% Completed</div>
                    <div class="text-2xl font-black text-emerald-600">{{ $totalCompleted }}</div>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('admin.beginner-guide.analytics') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-mutedText uppercase mb-1">Search Student</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email or Mobile..." class="w-full h-10 px-3 text-xs font-semibold rounded-xl border border-gray-200 focus:border-primary focus:ring-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-mutedText uppercase mb-1">Status</label>
                    <select name="status" class="w-full h-10 px-3 text-xs font-semibold rounded-xl border border-gray-200 focus:border-primary focus:ring-0 outline-none">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>100% Completed Only</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-mutedText uppercase mb-1">Filter Video</label>
                    <select name="video_id" class="w-full h-10 px-3 text-xs font-semibold rounded-xl border border-gray-200 focus:border-primary focus:ring-0 outline-none">
                        <option value="">All Videos</option>
                        @foreach($videos as $vid)
                            <option value="{{ $vid->id }}" {{ request('video_id') == $vid->id ? 'selected' : '' }}>{{ $vid->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="h-10 px-5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary/90 transition-all flex-1">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.beginner-guide.analytics') }}" class="h-10 px-3 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-black text-mainText text-sm uppercase tracking-wider">User Viewing Logs</h3>
                <span class="text-xs font-bold text-mutedText">{{ $views->total() }} Records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black uppercase tracking-wider text-mutedText">
                            <th class="py-4 px-6">Student</th>
                            <th class="py-4 px-6">Video Title / Category</th>
                            <th class="py-4 px-6">Watch Progress</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Last Viewed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium">
                        @forelse($views as $view)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($view->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-mainText">{{ $view->user->name ?? 'Deleted User' }}</div>
                                            <div class="text-[11px] text-mutedText opacity-75">{{ $view->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6">
                                    <div class="font-bold text-mainText">{{ $view->video->title ?? 'N/A' }}</div>
                                    @if(isset($view->video->category_rel))
                                        <span class="inline-block mt-0.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-100 text-gray-600">
                                            {{ $view->video->category_rel->name }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-6">
                                    <div class="w-36">
                                        <div class="flex justify-between items-center text-[11px] font-bold mb-1">
                                            <span class="text-mutedText">{{ $view->seconds }}s watched</span>
                                            <span class="{{ $view->completed ? 'text-emerald-600' : 'text-primary' }}">{{ $view->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $view->completed ? 'bg-emerald-500' : 'bg-primary' }}" style="width: {{ $view->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6">
                                    @if($view->completed)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200/60 shadow-sm">
                                            <i class="fas fa-check-circle text-emerald-500"></i> 100% Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200/60">
                                            <i class="fas fa-spinner fa-spin text-amber-500"></i> In Progress ({{ $view->progress_percentage }}%)
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-6 text-mutedText text-[11px] font-semibold">
                                    {{ $view->last_viewed_at ? $view->last_viewed_at->format('d M Y, h:i A') : $view->updated_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-mutedText font-semibold">
                                    <i class="fas fa-chart-bar text-3xl opacity-30 mb-2 block"></i>
                                    No viewing logs found matching criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($views->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $views->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
