@extends('layouts.admin')
@section('title', 'Edit Role')

@section('content')
<div class="container-fluid font-sans antialiased">
    {{-- TOP HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
        <div>
            <h2 class="text-2xl font-black text-mainText tracking-tight">Edit Role: <span class="text-primary capitalize">{{ $role->name }}</span></h2>
            <p class="text-sm text-mutedText mt-1 font-medium">Update permissions and access levels for the selected role.</p>
        </div>
        <a href="{{ route('admin.roles.index') }}"
            class="bg-white hover:bg-navy border border-primary/10 inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-xs font-black text-mutedText hover:text-primary transition-all duration-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-primary/10 shadow-xl overflow-hidden animate-fade-in">
        <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="p-8 md:p-12">
            @csrf
            @method('PATCH')

            {{-- ROLE NAME --}}
            <div class="mb-10 lg:w-1/2">
                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-3 ml-1">Role Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary/40">
                        <i class="fas fa-user-tag text-sm"></i>
                    </span>
                    <input type="text" name="name" value="{{ $role->name }}" required
                        class="w-full pl-12 pr-6 py-4 bg-navy/30 border border-primary/5 text-mainText placeholder-mutedText/40 rounded-2xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all duration-300 font-bold text-sm shadow-inner"
                        placeholder="e.g. Sales Manager, Course Editor">
                </div>
                @error('name')
                    <p class="mt-2 text-xs font-bold text-secondary ml-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PERMISSIONS SECTION --}}
            <div class="pt-8 border-t border-primary/5">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-lg font-black text-mainText tracking-tight">Modify Permissions</h3>
                        <p class="text-xs text-mutedText font-medium mt-1">Sync specific permissions to this role.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($permissions as $value)
                    <label class="relative group cursor-pointer">
                        <input type="checkbox" name="permission[]" value="{{ $value->name }}" class="peer sr-only"
                            {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                        <div class="flex items-center gap-4 p-4 bg-navy/30 border border-primary/5 rounded-2xl transition-all duration-300 peer-checked:bg-primary/5 peer-checked:border-primary peer-checked:shadow-lg peer-checked:shadow-primary/5 group-hover:bg-navy/50">
                            <div class="h-10 w-10 flex-shrink-0 bg-white rounded-xl flex items-center justify-center text-mutedText/40 group-hover:text-primary transition-colors peer-checked:text-primary">
                                <i class="fas fa-check-circle text-sm opacity-0 peer-checked:opacity-100 absolute"></i>
                                <i class="fas fa-shield-alt text-sm peer-checked:opacity-0"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-mainText/80 group-hover:text-mainText peer-checked:text-primary transition-colors capitalize truncate pr-2">
                                    {{ str_replace('manage-', 'Manage ', $value->name) }}
                                </p>
                                <p class="text-[9px] font-bold text-mutedText mt-0.5 tracking-tight uppercase opacity-60">System Core</p>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('permission')
                   <p class="mt-6 text-xs font-bold text-secondary">{{ $message }}</p>
                @enderror
            </div>

            {{-- SUBMIT --}}
            <div class="mt-12 pt-10 border-t border-primary/5 flex flex-col md:flex-row items-center gap-6 justify-between">
                <div class="flex items-center gap-3 text-mutedText font-medium">
                    <span class="h-2 w-2 rounded-full bg-secondary animate-pulse"></span>
                    <p class="text-xs leading-none">Security context updates immediately for all users in this role.</p>
                </div>
                <button type="submit"
                    class="w-full md:w-auto brand-gradient px-12 py-4 text-xs font-black uppercase tracking-widest text-white rounded-2xl shadow-xl shadow-primary/20 hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3">
                    <i class="fas fa-save h-3 w-3"></i>
                    Update Role Permissions
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
