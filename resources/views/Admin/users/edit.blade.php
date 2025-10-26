@extends('Admin.layout.header')
@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit User</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
            <input type="password" name="password" id="password" class="form-control">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label d-block">User Type</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typeAdmin" value="admin"
                    {{ old('type', $user->type) === 'admin' ? 'checked' : '' }}>
                <label class="form-check-label" for="typeAdmin">Admin</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typeUser" value="user"
                    {{ old('type', $user->type) === 'user' ? 'checked' : '' }}>
                <label class="form-check-label" for="typeUser">User</label>
            </div>
            @error('type')
                <br><small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Update User</button>
        <a href="{{ route('users.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>