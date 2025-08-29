@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Available Groups</h5>
                    <h1 class="display-4">{{ $availableGroups }}</h1>
                    <p class="card-text">Number of groups available to join.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Joined Groups</h5>
                    <h1 class="display-4">{{ $joinedGroups }}</h1>
                    <p class="card-text">Number of groups you have joined.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="col-xl-12 mb-4">
        <div class="content-title mt-4">
            <h3>Study Session Calendar</h3>
            @php
            $totalSessions = count($sessions);
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

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">📈 Uploads Analytics</h5>
            <div class="d-flex justify-content-between">
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="showChart('weekly')">Weekly</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showChart('monthly')">Monthly</button>
                </div>
                {{-- <div class="text-end mb-2">
                    <a href="{{ route('dashboard.export', ['type' => 'weekly']) }}"
                        class="btn btn-sm btn-outline-success me-2">Export
                        Weekly CSV</a>
                    <a href="{{ route('dashboard.export', ['type' => 'monthly']) }}"
                        class="btn btn-sm btn-outline-info">Export Monthly
                        CSV</a>
                </div> --}}
            </div>
            <canvas id="uploadChart" height="100"></canvas>
        </div>
    </div>


    <div class="content-title d-flex justify-content-between">
        <h4>Recent Files Added to Library</h4>
        {{-- <a href="{{route('user.study-groups.files.index', $group)}}">View All</a> --}}
    </div>

    {{-- Recent Uploads --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">📁 Recent Uploads</h5>
            @if($files->isEmpty())
            <p class="text-muted">No recent uploads.</p>
            @else
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>Size</th>
                            <th>Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <td>{{ $file->original_name }}</td>
                            <td>{{ $file->category }}</td>
                            <td>{{ formatFileSize($file->size) }}</td>
                            <td>{{ $file->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">🔄 Files Shared With You</h5>
            @if($recentShared->isEmpty())
            <p class="text-muted">No files have been shared with you recently.</p>
            @else
            <ul class="list-group list-group-flush">
                @foreach($recentShared as $shared)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        {{ $shared->file->original_name }}
                        <p class="small text-primary">{{ $shared->file->group->name }}</p>
                    </div>
                    <span class="small text-muted">{{ $shared->created_at->diffForHumans() }}</span>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: @json($formattedSessions),
        });
        calendar.render();
    });
</script>
@endsection