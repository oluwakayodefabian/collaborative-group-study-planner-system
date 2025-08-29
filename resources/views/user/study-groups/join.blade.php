<div class="card shadow">
    <div class="card-header">
        <h3>Join Study Group</h3>
    </div>
    <div class="card-body">
        <ul class="list-group">
            @forelse($studyGroups as $group)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><a href="{{ route('user.study-groups.show', $group) }}">{{ $group->name }}</a>
                    </h5>
                    <p class="mb-0 text-muted">Created: {{ $group->created_at->diffForHumans() }}</p>
                </div>
                <div class="d-flex">
                    <form action="{{ route('user.study-groups.join', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn text-white mr-2" style="background-color: #28a745"><i
                                class="fas fa-handshake"></i> Join</button>
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
            <li class="list-group-item text-danger lead">No study groups to join yet!</li>
            @endforelse
        </ul>
    </div>
</div>