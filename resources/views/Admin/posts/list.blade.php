@extends('Admin.layout.header')
@section('content')
<div class="container mt-4">
    @foreach($posts as $post)
        <div class="card post-card mb-4 shadow-sm">
            <div class="card-body">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $post->UserName }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}
                        </small>

                        <!-- Triple-dot dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.posts.edit', $post->PostId) }}">Edit</a></li>
                                <li>
                                    <form action="{{ route('admin.posts.delete', $post->PostId) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h6 class="mt-3 fw-semibold">{{ $post->PostTitle }}</h6>
                @php
                    if ($post->FileType === 'file') {
                        $extension = strtolower(pathinfo($post->PostFile, PATHINFO_EXTENSION));
                        $fileType = in_array($extension, ['mp4'])
                            ? 'video'
                            : (in_array($extension, ['jpg','jpeg','png']) ? 'image' : 'file');
                    } else {
                        $fileType = $post->FileType;
                    }
                @endphp

                @if($fileType === 'text')
                    <p class="mt-2">{{ $post->PostFile }}</p>

                @elseif($fileType === 'image')
                    <img src="{{ asset('storage/' . $post->PostFile) }}" alt="Post Image" class="post-img-video mt-3 rounded-3 shadow-sm w-100">

                @elseif($fileType === 'video')
                    <video controls class="post-img-video mt-3 w-100 rounded-3 shadow-sm">
                        <source src="{{ asset('storage/' . $post->PostFile) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>

                @else
                    <a href="{{ asset('storage/' . $post->file_path) }}" target="_blank" class="d-block mt-3 text-decoration-none text-primary">
                        Download File ({{ $extension ?? 'file' }})
                    </a>
                @endif

                {{-- Comments Section --}}
                <div class="comment-section mt-3">
                    <h6>Comments:</h6>
                    @php
                        $allComments = collect($post->PostComment);
                        $displayComments = $allComments->take(3);
                    @endphp

                    @foreach($displayComments as $comment)
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <strong style="font-weight:500; font-size:0.95rem;">{{ $comment->UserName ?? 'User' }}:</strong>
                                <span>{{ $comment->Comment ?? $comment }}</span>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.comment.edit', $comment->PCId) }}">Edit</a></li>
                                    <li>
                                        <form action="{{ route('admin.comment.delete', $comment->PCId) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach

                    @if($allComments->count() > 3)
                        <small>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#commentsModal{{ $post->PostId }}">
                                And {{ $allComments->count() - 3 }} more comments...
                            </a>
                        </small>
                    @endif
                </div>
                <!-- Modal -->
                <div class="modal fade" id="commentsModal{{ $post->PostId }}" tabindex="-1" aria-labelledby="commentsModalLabel{{ $post->PostId }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentsModalLabel{{ $post->PostId }}">All Comments</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @foreach($allComments as $comment)
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong style="font-weight:500; font-size:0.95rem;">{{ $comment->UserName ?? 'User' }}:</strong>
                                            <span>{{ $comment->Comment ?? $comment }}</span>
                                        </div>

                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.comment.edit', $comment->PCId) }}">Edit</a></li>
                                                <li>
                                                    <form action="{{ route('admin.comment.delete', $comment->PCId) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Comment Form --}}
                <form action="{{ route('admin.posts.comment', $post->PostId ?? $post->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="comment" class="form-control" placeholder="Write a comment..." required>
                        <button class="btn btn-primary" type="submit">Post</button>
                    </div>
                </form>

            </div>
        </div>
    @endforeach
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>