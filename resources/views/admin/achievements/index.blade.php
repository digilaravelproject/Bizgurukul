@extends('layouts.admin')
@section('title', 'Achievement Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="font-sans text-mainText min-h-screen space-y-8">

        {{-- Top Bar: Header & Create Action --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 animate-fade-in-down">
            <div class="space-y-1">
                <h2 class="text-3xl font-extrabold tracking-tight text-mainText">Achievements & Rewards</h2>
                <div class="flex items-center gap-2 text-sm font-medium">
                    <span class="text-mutedText">Gamification milestone system</span>
                    <span class="h-1 w-1 rounded-full bg-primary/30"></span>
                    <span class="text-primary font-bold">Total: {{ $achievements->count() }} milestones</span>
                </div>
            </div>

            <a href="{{ route('admin.achievements.create') }}"
                class="group relative inline-flex items-center justify-center gap-3 rounded-2xl brand-gradient px-8 py-4 text-xs font-black text-customWhite uppercase tracking-[2px] shadow-xl shadow-primary/30 transition-all duration-500 hover:shadow-primary/50 hover:-translate-y-1 active:scale-95 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="h-5 w-5 transition-transform duration-500 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    New Achievement
                </span>
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-700 ease-out skew-x-12"></div>
            </a>
        </div>

        {{-- Achievements Table --}}
        <div class="bg-surface border border-primary/10 rounded-[2.5rem] shadow-2xl shadow-primary/5 overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary/5 border-b border-primary/10">
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Priority</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Image</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Title</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Target (₹)</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Reward Type</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($achievements as $achievement)
                            <tr class="hover:bg-primary/5 transition-colors duration-300">
                                <td class="px-8 py-6">
                                    <span class="h-8 w-8 flex items-center justify-center rounded-full bg-primary/10 text-primary font-black text-sm">
                                        {{ $achievement->priority }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($achievement->reward_image)
                                        <img src="{{ $achievement->reward_image_url }}" alt="{{ $achievement->title }}" class="h-12 w-12 rounded-xl object-cover border border-primary/10 shadow-sm">
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-primary/5 flex items-center justify-center border border-primary/10">
                                            <svg class="h-6 w-6 text-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-mainText">{{ $achievement->title }}</div>
                                    <div class="text-xs text-mutedText font-medium">{{ $achievement->short_title }}</div>
                                </td>
                                <td class="px-8 py-6 font-black text-primary">₹{{ number_format($achievement->target_amount, 2) }}</td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest">
                                        {{ $achievement->reward_type }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               onchange="toggleStatus({{ $achievement->id }})"
                                               class="sr-only peer"
                                               {{ $achievement->status ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-mutedText/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                        <span class="ml-3 text-xs font-bold {{ $achievement->status ? 'text-success' : 'text-mutedText' }}" id="status-text-{{ $achievement->id }}">
                                            {{ $achievement->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.achievements.edit', $achievement) }}"
                                           class="h-10 w-10 flex items-center justify-center rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-customWhite transition-all duration-300 shadow-lg shadow-primary/5">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button onclick="confirmDelete({{ $achievement->id }})"
                                           class="h-10 w-10 flex items-center justify-center rounded-xl bg-error/10 text-error hover:bg-error hover:text-customWhite transition-all duration-300 shadow-lg shadow-error/5">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
        function toggleStatus(id) {
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
                    const textEl = document.getElementById(`status-text-${id}`);
                    if (data.status) {
                        textEl.innerText = 'Active';
                        textEl.classList.remove('text-mutedText');
                        textEl.classList.add('text-success');
                    } else {
                        textEl.innerText = 'Inactive';
                        textEl.classList.remove('text-success');
                        textEl.classList.add('text-mutedText');
                    }

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
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
