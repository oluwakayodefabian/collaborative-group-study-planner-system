@extends('layouts.user_layout')

@section('content')

<div class="content">
    <div class="container mt-4">
        <h3 class="mb-4">📁 Files Shared With Me</h3>

        @if($sharedFiles->isEmpty())
        <div class="alert alert-info">No files have been shared with you yet.</div>
        @else
        <div class="table-responsive">
            <table class="table table-striped align-middle" id="example2">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Shared By</th>
                        <th>Shared At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sharedFiles as $share)
                    <tr>
                        <td>{{ $share->file->original_name }}</td>
                        <td>{{ $share->sender->name }}</td>
                        <td>{{ $share->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('files.download', $share->file->id) }}" class="btn btn-sm btn-success">
                                Download
                            </a>
                            <a href="{{ route('files.preview', $share->file->id) }}" class="btn btn-sm btn-info">
                                Preview
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection