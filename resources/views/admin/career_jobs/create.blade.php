@extends('layouts.admin')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .ql-editor {
            min-height: 200px;
            font-family: inherit;
        }

        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: rgba(0, 0, 0, 0.05);
            background: #f8fafc;
        }

        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .select2-container--default .select2-selection--multiple {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 4px 8px;
            min-height: 45px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 8px 12px;
            height: 48px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            font-size: 0.875rem;
            font-weight: 500;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 12px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #3b82f6;
            ring: 2px;
            ring-color: rgba(59, 130, 246, 0.1);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            color: #1e40af;
            border-radius: 0.375rem;
            padding: 2px 8px;
            font-weight: 600;
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.career-jobs.index') }}"
            class="text-xs font-bold text-mutedText uppercase tracking-widest hover:text-primary transition-colors flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i> Back to Jobs
        </a>
        <h1 class="text-2xl font-extrabold text-mainText tracking-tight">Create <span class="text-primary">New Job</span>
        </h1>
        <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-bold">Post a new career opportunity</p>
    </div>

    <form action="{{ route('admin.career-jobs.store') }}" method="POST" enctype="multipart/form-data" id="jobForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-customWhite p-8 rounded-2xl border border-primary/5 shadow-sm space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Company
                                Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                                placeholder="e.g. Google">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Apply
                                Link (External URL)</label>
                            <input type="url" name="apply_link" value="{{ old('apply_link') }}" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                                placeholder="https://...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Job Title</label>
                            <select name="career_job_title_id" required class="select2-tags w-full">
                                <option value="">Select Title</option>
                                @foreach($titles as $title)
                                    <option value="{{ $title->id }}" {{ old('career_job_title_id') == $title->id ? 'selected' : '' }}>{{ $title->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Location</label>
                            <select name="career_job_location_id" required class="select2-tags w-full">
                                <option value="">Select Location</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('career_job_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Required
                            Skills</label>
                        <select name="skills[]" multiple required class="select2 w-full">
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ (is_array(old('skills')) && in_array($skill->id, old('skills'))) ? 'selected' : '' }}>{{ $skill->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Job
                            Description</label>
                        <div id="editor" class="bg-white rounded-xl overflow-hidden"></div>
                        <input type="hidden" name="description" id="description_input">
                    </div>
                </div>
            </div>

            <!-- Sidebar Options -->
            <div class="space-y-6">
                <div class="bg-customWhite p-8 rounded-2xl border border-primary/5 shadow-sm space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Company
                            Logo</label>
                        <input type="file" name="company_logo" accept="image/*"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-xs focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-primary/10 file:text-primary file:uppercase">
                        <p class="text-[9px] text-mutedText mt-2 font-bold italic">Max size: 2MB. Format: PNG, JPG.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Experience Level</label>
                        <select name="career_job_experience_id" required class="select2-tags w-full">
                            <option value="">Select Experience</option>
                            @foreach($experiences as $exp)
                                <option value="{{ $exp->id }}" {{ old('career_job_experience_id') == $exp->id ? 'selected' : '' }}>{{ $exp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2">Salary Range</label>
                        <select name="career_job_salary_id" class="select2-tags w-full">
                            <option value="">Undisclosed / Select Range</option>
                            @foreach($salaries as $sal)
                                <option value="{{ $sal->id }}" {{ old('career_job_salary_id') == $sal->id ? 'selected' : '' }}>{{ $sal->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Active Status</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full brand-gradient text-white py-4 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                        Publish Job
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                $('.select2').select2({
                    placeholder: "Select Required Skills",
                    tags: true
                });

                $('.select2-tags').select2({
                    tags: true,
                    placeholder: "Select or type to create new..."
                });

                var quill = new Quill('#editor', {
                    theme: 'snow',
                    placeholder: 'Write job details, responsibilities, requirements...'
                });

                $('#jobForm').on('submit', function () {
                    $('#description_input').val(quill.root.innerHTML);
                });

                @if(old('description'))
                    quill.root.innerHTML = {!! json_encode(old('description')) !!};
                @endif
            });
        </script>
    @endpush
@endsection