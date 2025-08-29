@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between mb-3">
        <h2 class="mb-4">Study Group: <span class="bg-primary rounded p-2">{{ $group->name }}</span></h2>
    </div>

    <div class="row">
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
                            <th>Creator</th>
                            <th>Start DateTime</th>
                            <th>End DateTime</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th colspan="3">Action</th>
                        </tr>
                    <tbody>
                        @foreach($group->studySessions as $session)
                        <tr>
                            <td>{{ $session->session_title }}</td>
                            <td>{{ $session->creator->name }}</td>
                            <td>{{ date('F d, Y H:i:s A', strtotime($session->start_time)) }}</td>
                            <td>{{ date('F d, Y H:i:s A', strtotime($session->end_time)) }}</td>
                            <td>
                                @php $expired = $session->end_time < \Carbon\Carbon::now(); @endphp @if ($expired) <span
                                    class="badge bg-danger">
                                    Expired</span>
                                    @else
                                    <span class="badge bg-success">Active</span>
                                    @endif
                            </td>
                            <td>{{ $session->created_at->diffForHumans() }}</td>
                            @if ($session->is_creator())
                            <td>
                                <button class="btn btn-danger"
                                    onclick="showDeleteSessionModal({{ $group->id }},{{ $session->id }}, '{{ $session->session_title }}')">Delete</button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>

                    </thead>
                </table>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="col-xl-12 mb-4">
            <div class="content-title mt-4">
                <h3>Study Session Calendar</h3>
                @php
                $totalSessions = count($studySessions);
                @endphp
                <h4>
                    @if ($totalSessions > 0)
                    <span class="text-success">Upcoming Study Sessions</span>
                    @else
                    <span class="text-danger">No Upcoming Study Sessions</span>
                    @endif
                </h4>
            </div>
            <div id="calendar"></div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        // events: @json($events),
        events: '/user/study-groups/{{ $group->id }}/sessions/calendar-events',
        });
        calendar.render();

        // Optional: Auto-refresh events every X seconds
        setInterval(function () {
        calendar.refetchEvents();
        }, 30000); // every 30 seconds
    });
</script>
@endsection