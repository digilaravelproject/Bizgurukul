@extends('layouts.admin')
@section('title', 'Create New Course')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="courseCreator()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-mainText tracking-tight">Create Course</h2>
            <p class="text-sm text-mutedText font-medium mt-1">Setup your course basics to get started.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-xs font-bold text-mutedText hover:text-primary transition-colors">← Back to List</a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-primary/5 border border-primary/10 overflow-hidden">
        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-10 space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Title --}}
                <div class="col-span-2">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Course Title</label>
                    <input type="text" name="title" required class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText border-2 border-transparent focus:border-primary focus:bg-white transition-all outline-none" placeholder="e.g. Master in Web Development">
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Category</label>
                    <select name="category_id" id="cat_selector" required @change="fetchSubCategories($event.target.value)"
                        class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText border-2 border-transparent focus:border-primary focus:bg-white transition-all outline-none appearance-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $c) <option value="{{$c->id}}">{{$c->name}}</option> @endforeach
                    </select>
                </div>

                {{-- Sub Category --}}
                <div class="relative">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Sub Category</label>
                    <select name="sub_category_id" id="sub_selector"
                        class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText border-2 border-transparent focus:border-primary focus:bg-white transition-all outline-none appearance-none">
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Base Price (INR)</label>
                    <input type="number" name="price" required class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText border-2 border-transparent focus:border-primary focus:bg-white transition-all outline-none" placeholder="₹ 4999">
                </div>

                {{-- Thumbnail --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Thumbnail</label>
                    <input type="file" name="thumbnail" required class="w-full text-xs text-mutedText file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary hover:file:text-white transition-all cursor-pointer">
                </div>

                {{-- Description --}}
                <div class="col-span-2">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText border-2 border-transparent focus:border-primary focus:bg-white transition-all outline-none" placeholder="What is this course about?"></textarea>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full brand-gradient py-5 rounded-2xl text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all transform hover:-translate-y-1">
                    Save & Add Lessons
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function courseCreator() {
    return {
        async fetchSubCategories(catId) {
            if(!catId) return;
            const res = await fetch(`/admin/courses/sub-categories/${catId}`);
            const data = await res.json();
            const subSelector = document.getElementById('sub_selector');
            subSelector.innerHTML = '<option value="">Select Sub Category</option>';
            data.forEach(item => {
                subSelector.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        }
    }
}
</script>
@endsection
