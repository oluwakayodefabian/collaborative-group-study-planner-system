@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between mb-3">
        <h2 class="mb-4">Study Group: {{ $group->name }}</h2>
    </div>

    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="content-title mt-4">
                <h4>Group Description</h4>
            </div>
            <p class="lead">{{ $group->description }}</p>
        </div>

        <div class="col-xl-12 mb-4">
            <div class="content-title mt-4">
                <h4>Group Members ({{ count($group->members) }})</h4>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><a href="{{ route('user.study-groups.chat.show', $group) }}"
                        class="btn btn-primary text-white btn-lg">Click to Chat with
                        group members</a></li>
                @forelse($group->members as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><a href="{{ route('profile.edit', $user) }}">{{ $user->name }}</a></h5>
                        <p class="mb-0 text-muted">Joined: {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </li>
                @empty
                <li class="list-group-item">No members yet</li>
                @endforelse
            </ul>
        </div>

        {{-- Study Sessions --}}
        <div class="col-xl-12 mb-4">
            <div class="content-title mt-4 d-flex justify-content-between">
                <h4>Study Sessions</h4>
                <a href="{{ route('user.study-groups.sessions.create', $group) }}"
                    class="btn btn-primary text-light mr-2">Add a new
                    study session</a>
            </div>

            <div class="table-responsive">
                <table id="example2" class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Start DateTime</th>
                            <th>End DateTime</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th colspan="3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->studySessions as $key => $session)
                        @if ($key == 4)
                        @break
                        @endif
                        <tr>
                            <td>{{ $session->session_title }}</td>
                            <td>{{ date('F d, Y H:i:s A', strtotime($session->start_time)) }}</td>
                            <td>{{ date('F d, Y H:i:s A', strtotime($session->end_time)) }}</td>
                            <td>
                                @php $expired = $session->end_time < \Carbon\Carbon::now(); @endphp @if ($expired) <span
                                    class="badge bg-danger">Expired</span>
                                    @else
                                    <span class="badge bg-success">Active</span>
                                    @endif
                            </td>
                            <td>{{ $session->created_at->diffForHumans() }}</td>
                            <td>
                                @if ($session->is_creator())
                                {{-- <a href="{{ route('user.study-groups.sessions.show', [$group, $session]) }}"
                                    class="btn btn-primary text-light mr-2">View</a> --}}
                                <button class="btn btn-danger"
                                    onclick="showDeleteSessionModal({{ $group->id }},{{ $session->id }}, '{{ $session->session_title }}')">Delete</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('user.study-groups.sessions.index', $group) }}"
                    class="btn btn-primary btn-lg btn-block w-50 mx-auto my-2 text-white">View All</a>
            </div>
        </div>

        {{-- Study Group Library (FILES) --}}
        <div class="col-xl-12">
            <div class="content-title mt-4">
                <h4>Study Group Library</h4>
            </div>
            <ul class="list-group">
                @forelse($group->studyFiles as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><a href="{{ route('user.study-groups.files.show', [$group, $file]) }}">{{
                                $file->original_name }}</a>
                        </h5>
                        <p class="mb-0 text-muted">Created: {{ $file->created_at->diffForHumans() }}</p>
                    </div>
                </li>
                @empty
                <li class="list-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <p>No Study files created yet</p>
                    </div>
                </li>
                @endforelse
            </ul>
            <a href="{{ route('user.study-groups.files.index', $group) }}"
                class="btn btn-lg btn-primary w-50 text-center d-block text-white mx-auto">View Group Study Library</a>
        </div>
    </div>

    @include('user.study-groups.sessions.delete')



</div>
@endsection

@section('scripts')
<script>
    function showDeleteSessionModal(groupId, sessionId, sessionName) {
        const modal = new bootstrap.Modal(document.getElementById('deleteSessionModal'));
        document.getElementById('deleteForm').action = `{{ route('user.study-groups.sessions.destroy', [':group_id', ':id']) }}`.replace(':group_id', groupId).replace(':id', sessionId);
        document.getElementById('deleteModalLabel').innerText = `Delete Study Session: ${sessionName}`;
        modal.show();
    }
</script>
@endsection