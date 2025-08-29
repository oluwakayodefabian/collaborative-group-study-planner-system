@extends('layouts.auth_layout')

@section('auth-content')

<div class="form-wrapper">

    <!-- logo -->
    <div id="logo">
        <a href="/"><img src="{{ asset('/logo.png') }}" alt="{{ config('app.name') }}"></a>
    </div>
    <!-- ./ logo -->


    <h5>Create account</h5>

    <!-- form -->
    <form method="post">
        @csrf
        @if (session()->has('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Name" required autofocus name="name">
            <p class="text-danger text-left">{{ $errors->first('name') }}</p>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" required autofocus name="username">
            <p class="text-danger text-left">{{ $errors->first('username') }}</p>
        </div>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" name="email" required>
            <p class="text-danger text-left">{{ $errors->first('email') }}</p>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation"
                required>
        </div>
        <button class="btn btn-primary btn-block">Register</button>
        <hr>
        <p class="text-muted">Already have an account?</p>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Sign in!</a>
    </form>
    <!-- ./ form -->


</div>

@endsection