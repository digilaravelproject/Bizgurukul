@extends('layouts.admin')

@section('title', 'Coupon Management')

@section('content')
    <div x-data="couponIndex()" x-init="init()" class="space-y-6 max-w-7xl mx-auto">

        {{-- 1. Header Row (Title & Create Button) --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Coupon Manager</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Manage discounts for Courses &
                    Bundles</p>
            </div>

            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-[#0666a3] transition-all text-sm active:scale-95 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Coupon
            </a>
        </div>

        {{-- 2. Search Bar Row (Next Line - Full Width) --}}
        <div class="p-1.5 bg-white border border-gray-200 shadow-sm rounded-2xl w-full">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()"
                    class="block w-full py-2.5 pr-3 text-sm font-medium border-0 rounded-xl pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 transition-all"
                    placeholder="Search by coupon code or course/bundle name...">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Old Spinner Loader --}}
        <div x-show="loading" x-transition x-cloak
            class="flex flex-col items-center justify-center py-24 bg-white border border-slate-100 rounded-[2rem]">
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-[#0777be]"></div>
            <span class="mt-4 text-sm font-bold text-slate-500 uppercase tracking-widest">Updating Coupon List...</span>
        </div>

        {{-- Table Container --}}
        <div x-show="!loading" x-transition id="coupons-table-container">
            @include('admin.coupons.partials.table', ['coupons' => $coupons])
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        function couponIndex() {
            return {
                search: '',
                loading: false,
                fetchData() {
                    this.loading = true;
                    fetch(`{{ route('admin.coupons.index') }}?search=${this.search}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('coupons-table-container').innerHTML = data.coupons;
                            this.loading = false;
                        }).catch(() => {
                            this.loading = false;
                        });
                },
                init() {
                    // Initial logic if needed
                }
            }
        }

        function confirmCouponDelete(id, code) {
            Swal.fire({
                title: 'Are you sure?',
                html: `Coupon <b>${code}</b> will be permanently removed!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete it'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('coupon-delete-form-' + id).submit();
            });
        }
    </script>
@endpush
