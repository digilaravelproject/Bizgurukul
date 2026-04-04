@extends('layouts.admin')
@section('title', 'Achievement Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="font-sans text-mainText min-h-screen space-y-8">

        {{-- Top Bar: Header & Create Action --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 animate-fade-in-down pb-2">
            <div class="space-y-0.5">
                <h2 class="text-2xl font-black tracking-tight text-mainText">Rewards & Milestones</h2>
                <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-mutedText">
                    <span class="text-primary/70">{{ $achievements->count() }} active targets</span>
                    <span class="h-0.5 w-0.5 rounded-full bg-primary/30"></span>
                    <span>Gamified growth system</span>
                </div>
            </div>

            <a href="{{ route('admin.achievements.create') }}"
                class="group relative inline-flex items-center justify-center gap-2 rounded-xl brand-gradient px-6 py-3 text-[10px] font-black text-customWhite uppercase tracking-widest shadow-lg shadow-primary/20 transition-all duration-300 hover:-translate-y-0.5 active:scale-95 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Milestone
                </span>
            </a>
        </div>

        {{-- Achievements Table --}}
        <div class="bg-surface border border-primary/10 rounded-[2rem] shadow-xl shadow-primary/5 overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse table-auto min-w-[1000px] lg:min-w-0">
                    <thead>
                        <tr class="bg-primary/[0.03] border-b border-primary/10">
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest w-16">#</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest w-16 text-center">Reward</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest">Milestone Info</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest">Target & Type</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest">Active Duration</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest text-center">Stats</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest text-center">Status</th>
                            <th class="px-5 py-4 text-[10px] font-black text-mutedText uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($achievements as $achievement)
                            <tr class="{{ $achievement->status ? 'bg-success/[0.04]' : 'opacity-70' }} hover:bg-primary/[0.06] transition-colors duration-300 group">
                                <td class="px-5 py-4">
                                    <span class="h-7 w-7 flex items-center justify-center rounded-lg bg-primary/10 text-primary font-black text-[11px]">
                                        {{ $achievement->priority }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-block relative">
                                        @if($achievement->reward_image)
                                            <img src="{{ $achievement->reward_image_url }}" alt="{{ $achievement->title }}" class="h-10 w-10 rounded-lg object-cover border border-primary/10 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-primary/5 flex items-center justify-center border border-primary/10 text-primary/30">
                                                <i class="fas fa-gift text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-extrabold text-mainText text-sm group-hover:text-primary transition-colors">{{ $achievement->title }}</div>
                                    <div class="text-[10px] text-mutedText font-bold uppercase tracking-tighter">{{ $achievement->short_title }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-black text-primary text-sm">₹{{ number_format($achievement->target_amount) }}</div>
                                    <div class="inline-flex mt-1 px-2 py-0.5 rounded-md bg-primary/5 text-primary text-[9px] font-black uppercase tracking-widest border border-primary/10">
                                        {{ $achievement->reward_type }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-success"></span>
                                            <span class="text-[10px] font-black text-mainText">{{ $achievement->start_date ? $achievement->start_date->format('d M y') : 'Immediate' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-error"></span>
                                            <span class="text-[10px] font-black text-mutedText">{{ $achievement->end_date ? $achievement->end_date->format('d M y') : 'Lifetime' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('admin.achievements.qualified', $achievement) }}"
                                       class="inline-flex flex-col items-center gap-0.5 group/btn transition-all">
                                        <span class="text-sm font-black text-success">{{ $achievement->userAchievements()->whereIn('status', ['unlocked', 'claimed'])->count() }}</span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter text-mutedText group-hover/btn:text-success">Qualified</span>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-center">
                                        <label class="relative inline-flex items-center cursor-pointer group">
                                            <input type="checkbox"
                                                   onchange="toggleStatus(this, {{ $achievement->id }})"
                                                   class="sr-only peer"
                                                   {{ $achievement->status ? 'checked' : '' }}>
                                            {{-- Custom Toggle Track --}}
                                            <div class="w-11 h-6 bg-mutedText/20 rounded-full peer border-2 border-transparent transition-all duration-500
                                                peer-checked:bg-success/10 peer-checked:border-success/30 
                                                shadow-inner 
                                                group-hover:shadow-primary/10"></div>
                                            {{-- Custom Toggle Ball --}}
                                            <div class="absolute top-1 left-1 w-4 h-4 bg-mutedText/40 rounded-full transition-all duration-500 ease-[cubic-bezier(0.68,-0.55,0.27,1.55)] shadow-sm
                                                peer-checked:translate-x-5 peer-checked:bg-success peer-checked:shadow-success/30
                                                group-hover:scale-110 active:scale-95"></div>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.achievements.edit', $achievement) }}"
                                           class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary/5 text-primary hover:bg-primary hover:text-customWhite transition-all duration-300">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button onclick="confirmDelete({{ $achievement->id }})"
                                           class="h-8 w-8 flex items-center justify-center rounded-lg bg-error/[0.05] text-error hover:bg-error hover:text-customWhite transition-all duration-300">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <form id="delete-form-{{ $achievement->id }}" action="{{ route('admin.achievements.destroy', $achievement) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-20 w-20 rounded-[2rem] bg-primary/5 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-2.022-.221-2.916-.621l-.548-.547z" />
                                            </svg>
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="text-lg font-black text-mainText">No achievement milestones yet</h4>
                                            <p class="text-sm font-medium text-mutedText">Create milestones to gamify user earnings.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleStatus(el, id) {
            fetch(`/admin/achievements/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Smooth Row State Transition
                    const row = el.closest('tr');
                    if (data.status) {
                        row.classList.add('bg-success/[0.04]');
                        row.classList.remove('opacity-70');
                    } else {
                        row.classList.remove('bg-success/[0.04]');
                        row.classList.add('opacity-70');
                    }

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        background: 'rgb(var(--color-surface))',
                        color: 'rgb(var(--color-mainText))'
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    el.checked = !el.checked; // Revert toggle on error
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                el.checked = !el.checked; // Revert toggle on error
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong!', 'error');
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this milestone will remove it from all users' progress.",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2.5rem] p-10 bg-surface border border-primary/10',
                    title: 'text-2xl font-black text-mainText uppercase tracking-tight',
                    htmlContainer: 'text-mutedText font-medium mt-3',
                    confirmButton: 'brand-gradient px-8 py-3 rounded-2xl text-customWhite font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:opacity-90 transition-all ml-4',
                    cancelButton: 'px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest text-mutedText hover:bg-primary/5 transition-all'
                },
                confirmButtonText: 'Yes, Delete Milestone',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
