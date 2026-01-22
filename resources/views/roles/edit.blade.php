@extends('layouts.admin-layout')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Edit Role</h6>
                <a class="btn btn-secondary btn-sm" href="{{ route('roles.index') }}"> Back</a>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('roles.update', $role->id) }}" class="ajax-form">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Name:</label>
                                <input type="text" name="name" value="{{ $role->name }}" class="form-control"
                                    placeholder="Role Name">
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Permissions:</label>
                                <br />
                                <div class="row mt-2">
                                    @foreach ($permissions as $value)
                                        <div class="col-md-3 mb-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                                    class="form-checkbox h-5 w-5 text-blue-600"
                                                    {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                <span class="ml-2 text-gray-700">{{ $value->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <span class="text-danger error-text permission_error"></span>
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
