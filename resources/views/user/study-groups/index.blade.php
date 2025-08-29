@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between">
        <h2 class="mb-4">Study Groups</h2>
    </div>

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active border" id="pills-create-tab" data-toggle="pill" data-target="#pills-create"
                type="button" role="tab" aria-controls="pills-home" aria-selected="true">Create a study group</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link border" id="pills-list-tab" data-toggle="pill" data-target="#pills-list" type="button"
                role="tab" aria-controls="pills-list" aria-selected="false">Join Study Groups</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-create" role="tabpanel" aria-labelledby="pills-create-tab">
            @include('user.study-groups.create')
        </div>
        <div class="tab-pane fade" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">
            @include('user.study-groups.join')
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="content-title mt-4">
                <h4>My Study Groups</h4>
            </div>

            <ul class="list-group">
                @forelse($myStudyGroups as $group)
                <li class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row">
                    <div>
                        <h5 class="mb-0"><a href="{{ route('user.study-groups.show', $group) }}">{{ $group->name }}</a>
                        </h5>
                        <p class="mb-0 text-muted">Created: {{ $group->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('user.study-groups.show', $group) }}" class="btn btn-info text-light mr-2"><i
                                class="fas fa-eye"></i>View</a>
                        <a href="{{ route('user.study-groups.chat.show', $group) }}"
                            class="btn btn-dark text-light mr-2"><i class="fas fa-comments"></i>Chat Room</a>
                        <form action="{{ route('user.study-groups.leave', $group) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger text-light mr-2"><i
                                    class="fas fa-trash"></i>Leave Group</button>
                        </form>
                        @if ($group->is_group_owner())
                        <button type="button" onclick="showEditModal({{ $group->id }})"
                            class="btn btn-primary text-light mr-2"><i class="fas fa-edit"></i>Edit</button>
                        <button type="button" id="delete-study-group-{{ $group->id }}" class="btn btn-danger"
                            onclick="showDeleteModal({{ $group->id }}, '{{ $group->name }}')"><i
                                class="fas fa-trash"></i>Delete</button>
                        @endif
                    </div>
                </li>
                @empty
                <li class="list-group-item text-danger lead">You have not created or join any study groups yet!</li>
            </ul>
            @endforelse
        </div>
    </div>
    @if ($myStudyGroups->count() !== 0)
    {{-- Delete Modal --}}
    @include('user.study-groups.delete')

    {{-- Edit Modal --}}
    @include('user.study-groups.edit')
    @endif

</div>
@endsection

@section('scripts')
<script>
    function showDeleteModal(groupId, groupName) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('deleteForm').action = `{{ route('user.study-groups.destroy', ':id') }}`.replace(':id', groupId);
        document.getElementById('deleteModalLabel').innerText = `Delete Study Group: ${groupName}`;
        modal.show();
    }

    function showEditModal(groupId) {
        const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
        document.getElementById('editForm').action = `{{ route('user.study-groups.update', ':id') }}`.replace(':id', groupId);
        modal.show();
    }
</script>
@endsection