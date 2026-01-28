@extends('layouts.admin')
@section('content')
    <div x-data="bundleHandler()" class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Create New Package</h2>
            <a href="{{ route('admin.courses.index') }}" class="text-sm font-bold text-slate-500">← Cancel</a>
        </div>

        <form action="{{ route('admin.bundles.store') }}" method="POST"
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Package Name</label>
                <input type="text" name="title" required
                    class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase text-[10px]">Select Available Courses
                    (Value: ₹<span x-text="totalValue">0</span>)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    @foreach ($availableCourses as $course)
                        <label
                            class="flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-[#0777be] transition-all">
                            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                @change="toggleCourse($event, {{ $course->price }})"
                                class="w-5 h-5 rounded border-slate-300 text-[#0777be]">
                            <div class="ml-3">
                                <p class="text-xs font-black text-slate-800">{{ $course->title }}</p>
                                <p class="text-[10px] text-slate-500 uppercase">₹{{ number_format($course->price) }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Final Offer Price (INR)</label>
                    <input type="number" name="price" x-model="bundlePrice" required
                        class="w-full rounded-2xl border-slate-200 px-5 py-4 font-black text-[#0777be]">
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-sm font-bold text-slate-700">Live Status</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#0777be] text-white py-5 rounded-3xl font-black shadow-lg uppercase active:scale-95 transition-all">Create
                Bundle</button>
        </form>
    </div>

    <script>
        function bundleHandler() {
            return {
                totalValue: 0,
                bundlePrice: 0,
                toggleCourse(e, price) {
                    const p = parseFloat(price);
                    e.target.checked ? this.totalValue += p : this.totalValue -= p;
                    this.totalValue = parseFloat(this.totalValue.toFixed(2));
                    this.bundlePrice = this.totalValue; // Auto-fill price
                }
            }
        }
    </script>
@endsection
