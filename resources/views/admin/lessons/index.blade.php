@extends('layouts.admin')

@section('content')
    <div x-data="lessonManagement('{{ route('admin.lessons.index', $course->id) }}')" x-init="init()" class="space-y-6">

        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Lessons for: {{ $course->title }}</h2>
            <a href="{{ route('admin.lessons.create', $course->id) }}"
                class="bg-[#0777be] text-white px-5 py-2 rounded-xl font-bold shadow-md text-sm">
                + Add Lesson
            </a>
        </div>

        {{-- Loader Spinner --}}
        <div x-show="loading" x-cloak class="flex flex-col items-center justify-center py-20 bg-white border rounded-xl">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#0777be]"></div>
            <p class="mt-3 text-sm text-gray-500">Loading Lessons...</p>
        </div>

        {{-- Table Container --}}
        <div x-show="!loading" id="lessons-table-container">
            @include('admin.lms.lessons.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function lessonManagement(apiUrl) {
            return {
                loading: false,
                baseUrl: apiUrl,

                fetchData(page = 1) {
                    this.loading = true;
                    // URL mein page param add karein
                    const fetchUrl = `${this.baseUrl}?page=${page}`;

                    fetch(fetchUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('lessons-table-container').innerHTML = html;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                        });
                },

                init() {
                    // Pagination clicks handle karein
                    document.getElementById('lessons-table-container').addEventListener('click', (e) => {
                        const link = e.target.closest('.lesson-pagination a');
                        if (link) {
                            e.preventDefault();
                            const page = new URL(link.href).searchParams.get('page');
                            this.fetchData(page);
                        }
                    });
                }
            }
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Delete Lesson?',
                text: `Are you sure you want to delete "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
