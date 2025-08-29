@extends('layouts.auth_layout')

@section('auth-content')



<div class="form-wrapper">

    <!-- logo -->
    <div id="logo">
        <a href="/"><img src="{{ asset('/logo.png') }}" alt="{{ config('app.name') }}"></a>
    </div>
    <!-- ./ logo -->


    <h5>Sign in</h5>

    <!-- form -->
    <form method="post">
        @csrf
        @if ($errors->has('email'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('email') }}</div>
        @endif
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Email/Username" name="email" required autofocus>
            {{-- <p class="text-danger text-left">{{ $errors->first('email') }}</p> --}}
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
        </div>
        <div class="form-group d-flex justify-content-between">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked="" name="remember" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}">Reset password</a>
        </div>
        <button class="btn btn-primary btn-block">Sign in</button>
        <hr>
        <p class="text-muted">Don't have an account?</p>
        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Register now!</a>
    </form>
    <!-- ./ form -->


</div>
@endsection