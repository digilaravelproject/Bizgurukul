@extends('layouts.admin-layout')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="text-2xl font-bold">Create New User</h2>
            <a class="btn btn-secondary" href="{{ route('users.index') }}"> Back</a>
        </div>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name"
                        class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="form-control mt-1" placeholder="Email">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" class="form-control mt-1" placeholder="Password">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="confirm-password" class="form-control mt-1" placeholder="Confirm Password">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="roles[]" class="form-control mt-1" multiple>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple roles.</small>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-4">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow">
                        Submit User
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
