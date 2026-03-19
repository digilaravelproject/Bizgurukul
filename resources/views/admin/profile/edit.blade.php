@extends('layouts.admin')

@section('content')
<div class="space-y-8 font-sans text-mainText pb-12">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Profile Settings</h1>
            <p class="text-mutedText mt-1 text-sm">Update your administrator account name, email and password.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-8">

        {{-- UPDATE PROFILE INFORMATION --}}
        <div class="bg-surface rounded-2xl p-8 shadow-sm border border-primary/10 relative overflow-hidden group">
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-primary/5 rounded-full group-hover:bg-primary/10 transition-colors duration-500"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-primary/10 rounded-xl text-primary shadow-sm shadow-primary/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-mainText">Profile Information</h3>
                        <p class="text-xs text-mutedText uppercase tracking-widest font-bold">Manage Account Details</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Admin Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full h-12 bg-navy border border-primary/20 rounded-xl px-4 text-mainText focus:border-primary focus:ring-0 transition-all font-semibold placeholder-mutedText/50">
                        @error('name') <p class="mt-2 text-xs text-secondary font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full h-12 bg-navy border border-primary/20 rounded-xl px-4 text-mainText focus:border-primary focus:ring-0 transition-all font-semibold placeholder-mutedText/50">
                        @error('email') <p class="mt-2 text-xs text-secondary font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="h-12 w-full bg-primary text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-lg shadow-primary/25 hover:bg-secondary hover:shadow-secondary/25 transition-all duration-300">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- UPDATE PASSWORD --}}
        <div class="bg-surface rounded-2xl p-8 shadow-sm border border-orange-500/10 relative overflow-hidden group">
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-orange-500/5 rounded-full group-hover:bg-orange-500/10 transition-colors duration-500"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-orange-500/10 rounded-xl text-orange-600 shadow-sm shadow-orange-500/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-mainText">Security</h3>
                        <p class="text-xs text-mutedText uppercase tracking-widest font-bold">Update Password</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full h-12 bg-navy border border-primary/20 rounded-xl px-4 text-mainText focus:border-primary focus:ring-0 transition-all font-semibold">
                        @error('updatePassword.current_password') <p class="mt-2 text-xs text-secondary font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">New Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full h-12 bg-navy border border-primary/20 rounded-xl px-4 text-mainText focus:border-primary focus:ring-0 transition-all font-semibold">
                        @error('updatePassword.password') <p class="mt-2 text-xs text-secondary font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full h-12 bg-navy border border-primary/20 rounded-xl px-4 text-mainText focus:border-primary focus:ring-0 transition-all font-semibold">
                        @error('updatePassword.password_confirmation') <p class="mt-2 text-xs text-secondary font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="h-12 w-full bg-orange-600 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-lg shadow-orange-600/25 hover:bg-orange-700 hover:shadow-orange-700/25 transition-all duration-300">
                            Update Security
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
