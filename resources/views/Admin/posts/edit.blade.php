@extends('Admin.layout.header')
@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Admin Edit Post</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.posts.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $data->title) }}" required>
                </div>
                @if($data->file_type == 'text')
                <div class="mb-3">
                    <label class="form-label">Content (optional)</label>
                    <textarea name="content" class="form-control" rows="4">{{ old('content', $data->file_path ?? '') }}</textarea>
                </div>
                @else
                <div class="mb-3">
                    <label class="form-label">Change File (optional)</label>
                    <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.mp4">
                    <small class="text-muted">Allowed types: JPG, JPEG, PNG, MP4 | Max size: 5MB</small>
                </div>
                @endif
                @if($data->file_path && $data->file_type =='file')
                    <div class="mb-3">
                        <label class="form-label">Current File</label><br>
                        @php
                            $extension = strtolower(pathinfo($data->file_path, PATHINFO_EXTENSION));
                            $fileType = in_array($extension, ['mp4']) ? 'video' : (in_array($extension, ['jpg','jpeg','png']) ? 'image' : 'file');
                        @endphp

                        @if($fileType === 'image')
                            <img src="{{ asset('storage/' . $data->file_path) }}" alt="Post Image" class="img-fluid rounded" style="max-height: 250px;">
                        @elseif($fileType === 'video')
                            <video controls class="w-100 rounded" style="max-height: 250px;">
                                <source src="{{ asset('storage/' . $data->file_path) }}" type="video/mp4">
                            </video>
                        @endif
                    </div>
                @endif

                <div class="d-flex justify-content-between">
                    <a href="{{ route('post.list') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>