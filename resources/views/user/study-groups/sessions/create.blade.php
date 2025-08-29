@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="card shadow">
        <div class="card-header">
            <h1>Create Study Session</h1>
        </div>

        <div class="card-body">
            <form action="{{ route('user.study-groups.sessions.store', $studyGroup) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="session_title" class="form-label">Session Title</label>
                    <input type="text" class="form-control" id="session_title" name="session_title"
                        placeholder="Enter session title">
                    <p class="text-danger">{{ $errors->first('session_title') }}</p>
                </div>
                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Date and Time</label>
                    <input type="datetime-local" class="form-control" id="start_time" name="start_time">
                    <p class="text-danger">{{ $errors->first('start_time') }}</p>
                </div>
                <div class="mb-3">
                    <label for="end_time" class="form-label">End Date and Time</label>
                    <input type="datetime-local" class="form-control" id="end_time" name="end_time">
                    <p class="text-danger">{{ $errors->first('end_time') }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="end_time" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Enter a brief description about the study group"></textarea>
                    <p class="text-danger">{{ $errors->first('description') }}</p>
                </div>

                <button type="submit" class="btn btn-primary btn-lg d-block w-50 mx-auto">Add Session</button>
            </form>
        </div>
    </div>


</div>
@endsection