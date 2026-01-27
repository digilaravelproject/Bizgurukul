<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <h3 id="formTitle" class="text-lg font-bold text-slate-800 mb-4">Create New Course</h3>
    <form id="courseForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <input type="hidden" name="id" id="course_id">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Title</label>
            <input type="text" name="title" id="title" required
                class="w-full border-slate-200 rounded-xl text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description</label>
            <input type="text" name="description" id="description"
                class="w-full border-slate-200 rounded-xl text-sm">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold">Save Course</button>
    </form>
</div>

<script>
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.courses.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Success', res.success, 'success');
                $('#courseForm')[0].reset();
                $('#course_id').val('');
                loadCourses(); // Dusri file ka function call hoga table refresh karne ke liye
            }
        });
    });
</script>
