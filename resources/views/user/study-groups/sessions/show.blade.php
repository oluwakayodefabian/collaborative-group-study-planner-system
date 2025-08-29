@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $session->session_title }}</h5>
            <small class="text-white">
                {{ $session->start_time->format('D, M d, Y h:i A') }} -
                {{ $session->end_time->format('D, M d, Y h:i A') }}
            </small>
        </div>
        <div class="card-body">
            <h4><strong>Created by:</strong> {{ Str::ucfirst($session->creator->username) }}</h4>

            <p><strong>This session belongs to Study Group:</strong> {{ $session->group->name }}</p>

            @if($session->description)
            <p><strong>Description:</strong> {{ $session->description }}</p>
            @endif

            @if($session->location)
            <p><strong>Location / Link:</strong>
                <a href="{{ $session->location }}" target="_blank">{{ $session->location }}</a>
            </p>
            @endif

            <p><strong>Participants ({{ count($session->participants ?? []) }}):</strong></p>
            <ul class="list-group">
                @foreach($session->participants ?? [] as $participantId)
                @php $user = \App\Models\User::find($participantId); @endphp
                <li class="list-group-item">{{ $user ? $user->username : 'Unknown User' }}</li>
                @endforeach
            </ul>

            <div class="mt-4 d-flex">
                @php
                $joined = in_array(auth()->id(), $session->participants ?? []);
                @endphp

                <form method="POST"
                    action="{{ route('user.study-groups.sessions.toggle-participation', [$group,$session->id]) }}"
                    class="mr-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-{{ $joined ? 'danger' : 'success' }}">
                        {{ $joined ? 'Leave Session' : 'Join Session' }}
                    </button>
                </form>
                @if ($joined)
                <a href="{{ route('user.study-sessions.chat.show', $session) }}"
                    class="btn btn-primary text-white">Study Session
                    Chat Room</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@endsection