@extends('layouts.admin')
@section('content')
<h3>Edit: {{ $course->title }}</h3>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item"><a class="nav-link {{$activeTab=='basic'?'active':''}}" href="#basic" data-toggle="tab">Basic</a></li>
  <li class="nav-item"><a class="nav-link {{$activeTab=='lessons'?'active':''}}" href="#lessons" data-toggle="tab">Lessons</a></li>
  <li class="nav-item"><a class="nav-link {{$activeTab=='resources'?'active':''}}" href="#resources" data-toggle="tab">Resources</a></li>
  <li class="nav-item"><a class="nav-link {{$activeTab=='settings'?'active':''}}" href="#settings" data-toggle="tab">Settings & Price</a></li>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade {{$activeTab=='basic'?'show active':''}}" id="basic">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group"><label>Title</label><input type="text" name="title" value="{{$course->title}}" class="form-control"></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{$course->description}}</textarea></div>
            <button class="btn btn-success">Update</button>
        </form>
    </div>

    <div class="tab-pane fade {{$activeTab=='lessons'?'show active':''}}" id="lessons">
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#lessonModal">+ Add Lesson</button>
        <table class="table">
            <thead><tr><th>Title</th><th>Type</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($course->lessons as $l)
                <tr>
                    <td>{{$l->title}}</td>
                    <td>{{ucfirst($l->type)}}</td>
                    <td>
                        @if($l->type == 'video' && !$l->hls_path) <span class="badge badge-warning">Processing...</span>
                        @else <span class="badge badge-success">Ready</span> @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade {{$activeTab=='resources'?'show active':''}}" id="resources">
        <form action="{{ route('admin.courses.resource.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <div class="row">
                <div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="Resource Title" required></div>
                <div class="col-md-4"><input type="file" name="file" class="form-control" required></div>
                <div class="col-md-2"><button class="btn btn-primary">Upload</button></div>
            </div>
        </form>
        <ul>
            @foreach($course->resources as $r) <li>{{$r->title}} (<a href="{{asset('storage/'.$r->file_path)}}" target="_blank">Download</a>)</li> @endforeach
        </ul>
    </div>

    <div class="tab-pane fade {{$activeTab=='settings'?'show active':''}}" id="settings">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf @method('PUT')
            <h4>Pricing</h4>
            <div class="row">
                <div class="col-md-4"><label>Price</label><input type="number" name="price" value="{{$course->price}}" class="form-control"></div>
                <div class="col-md-4"><label>Discount Value</label><input type="number" name="discount_value" value="{{$course->discount_value}}" class="form-control"></div>
                <div class="col-md-4"><label>Type</label>
                    <select name="discount_type" class="form-control">
                        <option value="fixed" {{$course->discount_type=='fixed'?'selected':''}}>Fixed</option>
                        <option value="percent" {{$course->discount_type=='percent'?'selected':''}}>Percent</option>
                    </select>
                </div>
            </div>
            <hr>
            <h4>Certificate</h4>
            <div class="form-check">
                <input type="checkbox" name="certificate_enabled" value="1" {{$course->certificate_enabled?'checked':''}}> Enable Certificate
            </div>
            <button class="btn btn-success mt-3">Save Settings</button>
        </form>
    </div>
</div>

<div class="modal fade" id="lessonModal">
    <div class="modal-dialog">
        <form action="{{ route('admin.courses.lesson.store', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Lesson</h5></div>
                <div class="modal-body">
                    <input type="text" name="title" class="form-control mb-2" placeholder="Title" required>
                    <select name="type" class="form-control mb-2" onchange="if(this.value=='video'){$('#v').show();$('#d').hide()}else{$('#v').hide();$('#d').show()}">
                        <option value="video">Video</option><option value="document">PDF/Doc</option>
                    </select>
                    <div id="v"><label>Video File</label><input type="file" name="video_file" class="form-control"></div>
                    <div id="d" style="display:none"><label>Document File</label><input type="file" name="document_file" class="form-control"></div>
                    <label class="mt-2">Thumbnail</label><input type="file" name="thumbnail" class="form-control">
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Add</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
