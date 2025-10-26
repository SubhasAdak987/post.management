@extends('Admin.layout.header')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4 text-center">Admin Dashboard Overview</h3>

    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('users.list') }}" class="text-decoration-none">
                <div class="card card-hover shadow-sm border-0 p-3">
                    <h5 class="text-muted">Total Users</h5>
                    <h2 class="fw-bold text-primary">{{ $totalUsers }}</h2>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('post.list') }}" class="text-decoration-none">
                <div class="card card-hover shadow-sm border-0 p-3">
                    <h5 class="text-muted">Total Posts</h5>
                    <h2 class="fw-bold text-success">{{ $totalPosts }}</h2>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-hover shadow-sm border-0 p-3">
                <h5 class="text-muted">Total Comments</h5>
                <h2 class="fw-bold text-danger">{{ $totalComments }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

