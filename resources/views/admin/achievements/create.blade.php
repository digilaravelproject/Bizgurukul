@extends('layouts.admin')
@section('title', 'Create Achievement')

@section('content')
    <div class="font-sans text-mainText min-h-screen space-y-8 animate-fade-in">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.achievements.index') }}"
               class="h-12 w-12 flex items-center justify-center rounded-2xl bg-surface border border-primary/10 text-mutedText hover:text-primary hover:border-primary/30 transition-all duration-300 shadow-xl shadow-primary/5">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-mainText">New Milestone</h2>
                <p class="text-sm font-medium text-mutedText">Define a new achievement goal for users.</p>
            </div>
        </div>

        <form action="{{ route('admin.achievements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left: Main Details --}}
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-surface border border-primary/10 rounded-[2.5rem] p-8 shadow-2xl shadow-primary/5 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Achievement Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                       class="w-full h-14 px-6 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all"
                                       placeholder="e.g., Gold Champion">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Short Title (Dashboard)</label>
                                <input type="text" name="short_title" value="{{ old('short_title') }}"
                                       class="w-full h-14 px-6 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all"
                                       placeholder="e.g., Gold">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Reward Description</label>
                            <textarea name="reward_description" rows="4" required
                                      class="w-full px-6 py-4 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all"
                                      placeholder="Describe the reward or the milestone details...">{{ old('reward_description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Target Earning (₹)</label>
                                <input type="number" name="target_amount" value="{{ old('target_amount') }}" required min="0" step="0.01"
                                       class="w-full h-14 px-6 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all"
                                       placeholder="50000">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Reward Type</label>
                                <select name="reward_type" required
                                        class="w-full h-14 px-6 bg-primary/5 border-none text-mainText font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all appearance-none cursor-pointer">
                                    <option value="cash" {{ old('reward_type') == 'cash' ? 'selected' : '' }}>Cash 💰</option>
                                    <option value="gift" {{ old('reward_type') == 'gift' ? 'selected' : '' }}>Gift 🎁</option>
                                    <option value="trip" {{ old('reward_type') == 'trip' ? 'selected' : '' }}>Trip ✈️</option>
                                    <option value="gadget" {{ old('reward_type') == 'gadget' ? 'selected' : '' }}>Gadget 📱</option>
                                    <option value="custom" {{ old('reward_type') == 'custom' ? 'selected' : '' }}>Custom Reward</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Sidebar Details --}}
                <div class="space-y-8">
                    <div class="bg-surface border border-primary/10 rounded-[2.5rem] p-8 shadow-2xl shadow-primary/5 space-y-6">
                        <div class="space-y-4">
                            <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1 flex items-center justify-between">
                                Reward Image
                                <span class="text-[10px] text-mutedText font-medium normal-case">Max 2MB</span>
                            </label>
                            <div x-data="{ photoName: null, photoPreview: null }" class="space-y-4">
                                <div x-show="photoPreview" class="h-48 w-full rounded-2xl border-2 border-dashed border-primary/20 overflow-hidden relative group">
                                    <img :src="photoPreview" class="h-full w-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" @click="photoPreview = null; photoName = null; $refs.photo.value = ''"
                                                class="bg-error text-white h-10 w-10 rounded-full flex items-center justify-center hover:scale-110 transition-transform">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div x-show="!photoPreview" @click="$refs.photo.click()"
                                     class="h-48 w-full rounded-2xl border-2 border-dashed border-primary/10 bg-primary/5 flex flex-col items-center justify-center cursor-pointer hover:border-primary/30 hover:bg-primary/10 transition-all group">
                                    <svg class="h-10 w-10 text-primary/20 group-hover:text-primary transition-colors mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs font-black text-primary/40 group-hover:text-primary uppercase tracking-widest">Upload Reward Image</p>
                                </div>

                                <input type="file" name="reward_image" x-ref="photo" class="hidden"
                                       @change="
                                            photoName = $event.target.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => { photoPreview = e.target.result; };
                                            reader.readAsDataURL($event.target.files[0]);
                                       ">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest pl-1">Priority Order</label>
                                <input type="number" name="priority" value="{{ old('priority', 0) }}" required min="0"
                                       class="w-full h-14 px-6 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-2xl transition-all"
                                       placeholder="0">
                            </div>

                            <div class="flex items-center justify-between p-4 bg-primary/5 rounded-2xl border border-primary/5">
                                <label class="text-xs font-black text-mainText uppercase tracking-widest">Active Status</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="status" value="0">
                                    <input type="checkbox" name="status" value="1" class="sr-only peer" checked>
                                    <div class="w-12 h-6 bg-mutedText/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="group relative w-full inline-flex items-center justify-center gap-3 rounded-[1.5rem] brand-gradient px-8 py-5 text-sm font-black text-customWhite uppercase tracking-[3px] shadow-2xl shadow-primary/30 transition-all duration-500 hover:shadow-primary/50 hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Achievement
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-700 ease-out skew-x-12"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
