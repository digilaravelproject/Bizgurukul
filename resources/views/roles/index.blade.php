@extends('layouts.admin-layout')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Role Management</h6>
            @can('role-create')
                <a class="btn btn-primary btn-sm" href="{{ route('roles.create') }}"> Create New Role</a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="roles-table" width="100%" cellspacing="0">
                    <thead class="bg-gray-200">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#roles-table').DataTable({
                processing: true,
                ajax: "{{ route('roles.index') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            let editUrl = "{{ route('roles.edit', ':id') }}".replace(':id', id);
                            let deleteUrl = "{{ route('roles.destroy', ':id') }}".replace(':id',
                            id);

                            return `
                            <div class="flex gap-2">
                                @can('role-edit')
                                    <a href="${editUrl}" class="btn btn-sm btn-info text-white">Edit</a>
                                @endcan
                                @can('role-delete')
                                    <button class="btn btn-sm btn-danger delete-btn" data-url="${deleteUrl}">Delete</button>
                                @endcan
                            </div>
                        `;
                        }
                    }
                ]
            });

            // Delete Logic
            $(document).on('click', '.delete-btn', function() {
                let url = $(this).data('url');
                if (confirm('Are you sure you want to delete this role?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        },
                        error: function() {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });
    </script>
@endpush
