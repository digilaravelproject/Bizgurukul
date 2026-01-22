@extends('layouts.guest')

@section('content')
    <h3 class="text-center mb-4 text-xl font-semibold text-gray-700">Sign In</h3>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label font-bold text-gray-600">Email Address</label>
            <input type="email" name="email" class="form-control p-2 border-gray-300 rounded" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label font-bold text-gray-600">Password</label>
            <input type="password" name="password" class="form-control p-2 border-gray-300 rounded" required>
        </div>

        <div class="flex items-center justify-between mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label text-sm text-gray-600" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">Forgot Password?</a>
            @endif
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700 border-0 py-2 font-bold">
                Log In
            </button>
        </div>

        <div class="text-center mt-3">
            <span class="text-gray-600 text-sm">Don't have an account?</span>
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Register</a>
        </div>
    </form>
@endsection
