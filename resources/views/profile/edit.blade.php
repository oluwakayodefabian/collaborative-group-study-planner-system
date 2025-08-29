@extends("layouts.user_layout")

@section("content")
<div class="content">
    <div class="row">
        <div class="col-md-8">
            <h2>Profile</h4>
                <form action="{{ route('profile.update') }}" method="POST" class="shadow p-4">
                    @csrf
                    @method('patch')
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                            value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter name"
                            value="{{ $user->username }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" type="email"
                            class="form-control" id="email" name="email" placeholder="Enter email"
                            value="{{ $user->email }}" aria-describedby="emailHelp">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
        </div>
    </div>
</div>
@endsection