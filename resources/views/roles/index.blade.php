@extends('layouts.admin')
@section('title', 'Role Management')

@section('content')
<div class="container-fluid font-sans antialiased text-gray-800">
    {{-- 1. TOP HEADER & ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Role Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage system roles and permissions.</p>
        </div>

        <div class="flex items-center gap-4">
            @can('role-create')
            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus text-xs"></i>
                <span>Create Role</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- 2. SIMPLE STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Roles</p>
                <h4 class="text-3xl font-bold text-gray-900" id="total-roles">-</h4>
            </div>
            <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Permissions</p>
                <h4 class="text-3xl font-bold text-gray-900" id="total-perms">-</h4>
            </div>
            <div class="h-12 w-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-key"></i>
            </div>
        </div>
    </div>

    {{-- 3. CLEAN TABLE COMPONENT --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Roles List</h3>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm text-gray-600" id="roles-table">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Role Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4 text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Data injected via DataTable --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#roles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.roles.index') }}",
                dataSrc: function(json) {
                    $('#total-roles').text(json.data.length);
                    // Using a dummy logic for perms as in your original code
                    $('#total-perms').text(json.data.length * 14 + 12);
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'id',
                    render: function(data) {
                        return `<span class="font-medium text-gray-900">#${data}</span>`;
                    },
                    className: 'px-6 py-4'
                },
                {
                    data: 'name',
                    render: function(data) {
                        return `<span class="font-medium text-gray-800 capitalize">${data}</span>`;
                    },
                    className: 'px-6 py-4'
                },
                {
                    data: 'name',
                    render: function(data) {
                        let isSuper = data.toLowerCase() === 'admin';
                        if(isSuper) {
                            return `<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">System Admin</span>`;
                        } else {
                            return `<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">Standard Role</span>`;
                        }
                    },
                    className: 'px-6 py-4'
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(id, type, row) {
                        let editUrl = "{{ route('admin.roles.edit', ':id') }}".replace(':id', id);
                        let deleteUrl = "{{ route('admin.roles.destroy', ':id') }}".replace(':id', id);

                        let actions = `<div class="flex justify-end gap-2">`;

                        @can('role-edit')
                        actions += `
                            <a href="${editUrl}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>`;
                        @endcan

                        @can('role-delete')
                        if(row.name.toLowerCase() !== 'admin') {
                            actions += `
                                <button class="p-2 text-gray-400 hover:text-red-600 transition-colors delete-btn" data-url="${deleteUrl}" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>`;
                        } else {
                            actions += `<span class="p-2 text-gray-300" title="Protected"><i class="fas fa-lock"></i></span>`;
                        }
                        @endcan

                        actions += `</div>`;
                        return actions;
                    },
                    className: 'px-6 py-4 text-right'
                }
            ],
            dom: '<"flex flex-col md:flex-row justify-between items-center p-4 border-b border-gray-100"lf>rt<"flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-100"ip>',
            language: {
                search: "",
                searchPlaceholder: "Search roles...",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Delete Logic
        $(document).on('click', '.delete-btn', function() {
            let url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message || 'The role has been deleted.',
                                'success'
                            );
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    });
</script>

<style>
    /* Minimal override for Datatables to match standard Tailwind */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25em 0.75em;
        margin-left: 2px;
        border-radius: 0.375rem;
        border: 1px solid transparent;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e5e7eb;
        border: 1px solid #d1d5db;
        color: #374151 !important;
    }
</style>
@endpush
