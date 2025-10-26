@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center fs-4">Register</div>
                <div class="card-body">

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <hr>

                    <!-- Social Login Buttons -->
                    <a href="{{ url('auth/google') }}" class="btn btn-danger w-100 mb-2">
                        <i class="bi bi-google"></i> Login with Google
                    </a>
                    <a href="{{ url('auth/facebook') }}" class="btn btn-primary w-100">
                        <i class="bi bi-facebook"></i> Login with Facebook
                    </a>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Already have an account? Login</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
