@extends('layouts.guest')

@section('content')
    <h3 class="text-center mb-4 text-xl font-semibold text-gray-700">Create Account</h3>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label font-bold text-gray-600">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Mobile No</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Date of Birth</label>
                <input type="date" name="dob" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">State</label>
                <select name="state_id" class="form-control">
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">City</label>
                <input type="text" name="city" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-bold text-gray-600">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="d-grid gap-2 mt-2">
            <button type="submit" class="btn btn-success bg-green-600 hover:bg-green-700 border-0 py-2 font-bold">
                Register Now
            </button>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Already registered? Login</a>
        </div>
    </form>
@endsection
