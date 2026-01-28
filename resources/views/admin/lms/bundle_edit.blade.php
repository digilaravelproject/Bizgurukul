@extends('layouts.admin')

@section('content')
    <div x-data="bundleEditHandler({{ old('price', $bundle->price) }}, {{ $bundle->courses->sum('price') }})" class="max-w-4xl mx-auto space-y-6">

        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Edit Package: {{ $bundle->title }}</h2>
            <a href="{{ route('admin.courses.index') }}"
                class="text-sm font-bold text-slate-500 hover:text-slate-800 transition">← Back to Dashboard</a>
        </div>



        <form action="{{ route('admin.bundles.store') }}" method="POST"
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
            @csrf
            <input type="hidden" name="id" value="{{ $bundle->id }}">

            {{-- Bundle Title --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Package Name</label>
                <input type="text" name="title" value="{{ old('title', $bundle->title) }}" required
                    class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10 transition-all">
            </div>

            {{-- Courses Selection Section --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase text-[10px] tracking-widest">
                    Courses in Package (Total Value: ₹<span x-text="totalValue">0</span>)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-slate-50 rounded-3xl border border-slate-100">
                    @foreach ($availableCourses as $course)
                        <label
                            class="relative flex items-center p-4 bg-white border border-slate-200 rounded-2xl cursor-pointer hover:border-[#0777be] transition-all group">
                            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                {{ in_array($course->id, old('course_ids', $currentCourseIds)) ? 'checked' : '' }}
                                @change="toggleCourse($event, {{ $course->price }})"
                                class="w-6 h-6 rounded-lg border-slate-300 text-[#0777be] focus:ring-[#0777be]/20">
                            <div class="ml-4">
                                <p class="text-xs font-black text-slate-800 group-hover:text-[#0777be] transition-colors">
                                    {{ $course->title }}</p>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tight">Price:
                                    ₹{{ number_format($course->price) }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Pricing & Status Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Final Offer Price (INR)</label>
                    <input type="number" name="price" x-model="bundlePrice" required
                        class="w-full rounded-2xl border-slate-200 px-5 py-4 font-black text-[#0777be] text-lg">
                </div>

                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-slate-700 uppercase tracking-tighter">Live Status</span>
                        <span class="text-[10px] text-slate-400 font-bold"
                            x-text="isLive ? 'Visible to Students' : 'Draft Mode'"></span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" x-model="isLive" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#0777be] text-white py-5 rounded-3xl font-black shadow-lg shadow-blue-100 uppercase tracking-widest active:scale-95 transition-all hover:bg-[#0666a3]">
                Update Package Settings
            </button>
        </form>
    </div>

    {{-- Toastr for Real-time Notifications --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        function bundleEditHandler(initialPrice, initialValue) {
            return {
                totalValue: initialValue,
                bundlePrice: initialPrice,
                isLive: {{ $bundle->is_published ? 'true' : 'false' }},
                toggleCourse(e, price) {
                    const p = parseFloat(price);
                    e.target.checked ? this.totalValue += p : this.totalValue -= p;
                    this.totalValue = parseFloat(this.totalValue.toFixed(2));
                    this.bundlePrice = this.totalValue; // Auto-update price field
                }
            }
        }
    </script>
@endsection
