@extends('layouts.admin')
@section('title', 'Edit Course')

@section('content')
<div x-data="{ activeTab: '{{ $activeTab }}' }" class="animate-fade-in max-w-6xl mx-auto">

    <div class="mb-8">
        <h2 class="text-2xl font-black text-mainText tracking-tight">Edit: <span class="text-primary">{{ $course->title }}</span></h2>
        <div class="flex gap-2 mt-4 overflow-x-auto pb-2 no-scrollbar">
            <template x-for="tab in ['basic', 'lessons', 'resources', 'settings']">
                <button @click="activeTab = tab"
                    :class="activeTab === tab ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white text-mutedText border border-primary/10 hover:bg-primary/5'"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    x-text="tab">
                </button>
            </template>
        </div>
    </div>

    {{-- TAB 1: BASIC --}}
    <div x-show="activeTab === 'basic'" x-cloak class="bg-white rounded-[2rem] border border-primary/10 shadow-xl p-8">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase mb-2 ml-1">Title</label>
                <input type="text" name="title" value="{{$course->title}}" class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText focus:bg-white focus:border-primary border-2 border-transparent transition-all outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase mb-2 ml-1">Description</label>
                <textarea name="description" rows="5" class="w-full rounded-2xl bg-navy px-5 py-4 text-sm font-bold text-mainText focus:bg-white focus:border-primary border-2 border-transparent transition-all outline-none">{{$course->description}}</textarea>
            </div>
            <button class="brand-gradient px-8 py-4 rounded-xl text-white text-[10px] font-black uppercase tracking-widest shadow-md">Update Basics</button>
        </form>
    </div>

    {{-- TAB 2: LESSONS --}}
    <div x-show="activeTab === 'lessons'" x-cloak x-data="{ showModal: false }" class="space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-[1.5rem] border border-primary/10 shadow-sm">
            <p class="text-sm font-bold text-mainText">Course Syllabus ({{ count($course->lessons) }} Items)</p>
            <button @click="showModal = true" class="bg-primary text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md">+ Add Lesson</button>
        </div>

        <div class="bg-white rounded-[2rem] border border-primary/10 overflow-hidden shadow-xl">
            <table class="w-full text-left">
                <thead class="bg-primary/5 text-[10px] font-black text-primary uppercase tracking-widest border-b border-primary/5">
                    <tr><th class="px-8 py-5">Lesson Title</th><th class="px-6 py-5">Type</th><th class="px-6 py-5">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @foreach($course->lessons as $l)
                    <tr class="hover:bg-primary/[0.02] transition-colors">
                        <td class="px-8 py-5 font-bold text-sm text-mainText">{{$l->title}}</td>
                        <td class="px-6 py-5 text-xs font-black uppercase text-mutedText tracking-tighter">{{$l->type}}</td>
                        <td class="px-6 py-5">
                            @if($l->type == 'video' && !$l->hls_path) <span class="text-[9px] font-black uppercase px-2 py-1 bg-amber-100 text-amber-600 rounded">Processing</span>
                            @else <span class="text-[9px] font-black uppercase px-2 py-1 bg-green-100 text-green-600 rounded">Ready</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Add Lesson Modal --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-6" x-cloak>
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="bg-white rounded-[2rem] w-full max-w-lg relative z-10 p-8 shadow-2xl border border-primary/10" x-data="{ lType: 'video' }">
                <h3 class="text-xl font-black text-mainText mb-6">Add New Lesson</h3>
                <form action="{{ route('admin.courses.lesson.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="text" name="title" required class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none" placeholder="Lesson Title">
                    <select name="type" x-model="lType" class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none">
                        <option value="video">Video Lecture</option>
                        <option value="document">PDF / Document</option>
                    </select>
                    <div x-show="lType === 'video'"><label class="text-[10px] font-black uppercase ml-1">Video File</label><input type="file" name="video_file" class="w-full text-xs mt-1"></div>
                    <div x-show="lType === 'document'"><label class="text-[10px] font-black uppercase ml-1">Document File</label><input type="file" name="document_file" class="w-full text-xs mt-1"></div>
                    <div class="pt-4 flex gap-2">
                        <button type="button" @click="showModal = false" class="flex-1 bg-navy py-3 rounded-xl font-black text-[10px] uppercase">Cancel</button>
                        <button class="flex-1 brand-gradient py-3 rounded-xl font-black text-[10px] uppercase text-white shadow-md">Add Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TAB 3: RESOURCES --}}
    <div x-show="activeTab === 'resources'" x-cloak class="space-y-6">
        <div class="bg-white p-8 rounded-[2rem] border border-primary/10 shadow-xl">
            <h4 class="text-sm font-black text-mainText uppercase tracking-widest mb-6">Upload Material</h4>
            <form action="{{ route('admin.courses.resource.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                <div class="md:col-span-1"><input type="text" name="title" class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none" placeholder="Title (e.g. Source Code)" required></div>
                <div class="md:col-span-1"><input type="file" name="file" class="w-full text-xs cursor-pointer" required></div>
                <button class="brand-gradient py-3.5 rounded-xl text-white text-[10px] font-black uppercase shadow-md">Upload</button>
            </form>
        </div>
        <div class="bg-white p-8 rounded-[2rem] border border-primary/10 shadow-xl">
            <h4 class="text-sm font-black text-mainText uppercase tracking-widest mb-6">Existing Resources</h4>
            <div class="space-y-3">
                @foreach($course->resources as $r)
                <div class="flex justify-between items-center p-4 bg-navy rounded-2xl border border-primary/5 hover:border-primary/20 transition-all">
                    <span class="text-sm font-bold text-mainText">{{$r->title}}</span>
                    <a href="{{asset('storage/'.$r->file_path)}}" target="_blank" class="text-[10px] font-black text-primary uppercase bg-white px-3 py-1.5 rounded-lg shadow-sm">Download</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB 4: SETTINGS --}}
    <div x-show="activeTab === 'settings'" x-cloak class="bg-white p-8 rounded-[2rem] border border-primary/10 shadow-xl">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div><label class="block text-[10px] font-black text-mutedText uppercase mb-2">Original Price</label><input type="number" name="price" value="{{$course->price}}" class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none"></div>
                <div><label class="block text-[10px] font-black text-mutedText uppercase mb-2">Discount Value</label><input type="number" name="discount_value" value="{{$course->discount_value}}" class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none"></div>
                <div><label class="block text-[10px] font-black text-mutedText uppercase mb-2">Discount Type</label>
                    <select name="discount_type" class="w-full rounded-xl bg-navy px-4 py-3 text-sm font-bold border-none">
                        <option value="fixed" {{$course->discount_type=='fixed'?'selected':''}}>Fixed (₹)</option>
                        <option value="percent" {{$course->discount_type=='percent'?'selected':''}}>Percent (%)</option>
                    </select>
                </div>
            </div>
            <div class="p-6 bg-navy rounded-2xl flex items-center gap-4">
                <input type="checkbox" id="cert" name="certificate_enabled" value="1" {{$course->certificate_enabled?'checked':''}} class="w-5 h-5 rounded text-primary focus:ring-primary">
                <label for="cert" class="text-sm font-bold text-mainText">Enable Automated Certification for this course</label>
            </div>
            <button class="brand-gradient px-10 py-4 rounded-xl text-white text-[10px] font-black uppercase tracking-widest shadow-lg">Save All Settings</button>
        </form>
    </div>
</div>
@endsection
