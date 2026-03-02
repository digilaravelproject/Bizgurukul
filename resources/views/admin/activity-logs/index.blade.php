@extends('layouts.admin')
@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid font-sans antialiased">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
        <div>
            <h2 class="text-2xl font-black text-mainText tracking-tight">Activity Logs</h2>
            <p class="text-sm text-mutedText mt-1 font-medium">Audit trail of system changes and user actions.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-primary/10 shadow-xl overflow-hidden mt-6 animate-fade-in relative min-h-[400px]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-mutedText">
                <thead class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                    <tr>
                        <th class="px-6 py-5">Date</th>
                        <th class="px-6 py-5">Log Name</th>
                        <th class="px-6 py-5">Description</th>
                        <th class="px-6 py-5">Subject</th>
                        <th class="px-6 py-5">Causer</th>
                        <th class="px-8 py-5 text-center">Properties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-primary/[0.02] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-mainText text-xs">{{ $activity->created_at->format('d M, Y') }}</div>
                            <div class="text-[10px] text-mutedText mt-0.5">{{ $activity->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-navy px-3 py-1 text-[10px] font-black text-mainText border border-primary/5">
                                {{ ucfirst($activity->log_name) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-xs text-mainText">
                            {{ $activity->description }}
                        </td>
                        <td class="px-6 py-4">
                            @if($activity->subject_type)
                                <div class="text-[10px] font-black tracking-wider text-primary">{{ class_basename($activity->subject_type) }}</div>
                                <div class="text-xs font-bold mt-0.5 text-mainText">ID: {{ $activity->subject_id }}</div>
                            @else
                                <span class="text-xs text-mutedText italic">System/Generic</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($activity->causer)
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-xl brand-gradient flex items-center justify-center text-white font-black text-xs shadow-sm">
                                        {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-mainText">{{ $activity->causer->name }}</div>
                                        <div class="text-[10px] text-mutedText">{{ $activity->causer->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs font-bold text-mutedText italic">System/Unknown</span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-center">
                            @if($activity->properties && count($activity->properties) > 0)
                                <button type="button"
                                    class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-primary bg-primary/5 hover:bg-primary/10 rounded-lg transition border border-primary/10"
                                    onclick='viewProperties(@json($activity->properties))'
                                >
                                    View changes
                                </button>
                            @else
                                <span class="text-xs text-mutedText/50 font-bold">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-mutedText/50 font-bold">No activity logs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="px-8 py-6 bg-primary/5 border-t border-primary/5 flex items-center justify-between">
            <span class="text-xs text-mutedText font-bold">Showing <span class="text-primary">{{ $activities->firstItem() }}</span> - <span class="text-primary">{{ $activities->lastItem() }}</span> of <span class="text-primary">{{ $activities->total() }}</span> logs</span>
            <div>
                {{ $activities->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Properties Modal -->
<div id="propertiesModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm transition-opacity" onclick="closePropertiesModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl rounded-[2rem] bg-white border border-primary/10 shadow-2xl overflow-hidden transform transition-all">
            <div class="bg-navy px-8 py-6 border-b border-primary/5 flex justify-between items-center">
                <h3 class="text-xl font-black text-mainText">Activity Changes</h3>
                <button onclick="closePropertiesModal()" class="text-mutedText hover:text-secondary bg-white rounded-2xl p-2 shadow-sm transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8">
                <pre id="propertiesContent" class="bg-navy/50 p-6 rounded-2xl text-xs text-mainText font-mono overflow-auto max-h-[60vh] border border-primary/5"></pre>
            </div>
        </div>
    </div>
</div>

<script>
    function viewProperties(properties) {
        document.getElementById('propertiesContent').textContent = JSON.stringify(properties, null, 4);
        document.getElementById('propertiesModal').style.display = 'block';
    }

    function closePropertiesModal() {
        document.getElementById('propertiesModal').style.display = 'none';
    }
</script>
@endsection
