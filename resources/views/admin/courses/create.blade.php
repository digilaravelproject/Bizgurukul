@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">Create New Course</div>
    <div class="card-body">
        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <select name="category_id" id="cat" class="form-control" required>
                        <option value="">Select</option>
                        @foreach($categories as $c) <option value="{{$c->id}}">{{$c->name}}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Sub Category</label>
                    <select name="sub_category_id" id="sub" class="form-control"></select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Thumbnail</label>
                    <input type="file" name="thumbnail" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Demo Video (Optional)</label>
                    <input type="file" name="demo_video" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
            </div>
            <button class="btn btn-primary">Save & Next</button>
        </form>
    </div>
</div>
<script>
    document.getElementById('cat').addEventListener('change', function() {
        fetch('/admin/courses/sub-categories/'+this.value)
        .then(res=>res.json()).then(data=>{
            let sub = document.getElementById('sub'); sub.innerHTML = '<option value="">Select</option>';
            data.forEach(d => sub.innerHTML += `<option value="${d.id}">${d.name}</option>`);
        });
    });
</script>
@endsection
