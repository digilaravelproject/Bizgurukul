@extends('layouts.admin-layout')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Users Management</h6>
            <a class="btn btn-success btn-sm" href="{{ route('users.create') }}"> Create New User</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="users-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            var table;
            $(document).ready(function() {
                table = $('#users-table').DataTable({
                    processing: true,
                    serverSide: false, // True karein agar Yajra use kar rahe hain
                    ajax: "{{ route('users.index') }}", // AJAX URL
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'roles',
                            render: function(data) {
                                return data.map(role =>
                                    `<span class="badge bg-success">${role.name}</span>`).join(' ');
                            }
                        },
                        {
                            data: 'id',
                            render: function(data, type, row) {
                                let editUrl = "{{ route('users.edit', ':id') }}".replace(':id', data);
                                return `
                            <a href="${editUrl}" class="btn btn-primary btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">Delete</button>
                        `;
                            }
                        }
                    ]
                });
            });
        </script>
    @endpush
@endsection
