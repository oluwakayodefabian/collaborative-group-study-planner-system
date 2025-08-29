@php
$files = $results;
@endphp
@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="container">
        <div class="page-header d-flex justify-content-between">
            <h2 class="mb-4">📁 File {{$title}}</h2>
            <a href="#" class="files-toggler">
                <i class="ti-menu"></i>
            </a>
        </div>
        {{-- @include('file.partials.search_form') --}}

        @if($results->count())
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Added on</th>
                        <th>Modified</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td>
                            <a href="{{ route('files.preview', $file->id) }}" class="d-flex align-items-center"
                                target="_blank" title="preview">
                                <figure class="avatar avatar-sm mr-3">
                                    <span class="avatar-title bg-warning text-black-50 rounded-pill">
                                        @if ($file->category == 'documents')
                                        <i class="ti-file"></i>
                                        @elseif ($file->category == 'images')
                                        <i class="ti-image"></i>
                                        @endif
                                    </span>
                                </figure>
                                <span class="d-flex flex-column">
                                    <span class="text-primary">{{ $file->original_name }}</span>
                                    <span class="small font-italic">{{ formatFileSize($file->size)
                                        }}</span>
                                </span>
                            </a>
                        </td>
                        <td>
                            <div class="badge bg-info-bright text-info">{{ $file->category }}</div>
                        </td>
                        <td>
                            @if ($file->is_private)
                            <span class="badge bg-danger">Private</span>
                            @else
                            <span class="badge bg-success">Public</span>
                            @endif
                        </td>
                        <td>{{ date('M d, Y H:iA', strtotime($file->created_at)) }}</td>
                        <td>{{ date('M d, Y H:iA', strtotime($file->updated_at)) }}</td>
                        {{-- <td>
                            <div class="avatar-group">
                                <figure class="avatar avatar-sm" title="Lisle Essam" data-toggle="tooltip">
                                    <img src="../../assets/media/image/user/women_avatar2.jpg" class="rounded-circle"
                                        alt="image">
                                </figure>
                                <figure class="avatar avatar-sm" title="Baxie Roseblade" data-toggle="tooltip">
                                    <img src="../../assets/media/image/user/man_avatar5.jpg" class="rounded-circle"
                                        alt="image">
                                </figure>
                                <figure class="avatar avatar-sm" title="Jo Hugill" data-toggle="tooltip">
                                    <img src="../../assets/media/image/user/man_avatar1.jpg" class="rounded-circle"
                                        alt="image">
                                </figure>
                                <figure class="avatar avatar-sm" title="Cullie Philcott" data-toggle="tooltip">
                                    <img src="../../assets/media/image/user/women_avatar5.jpg" class="rounded-circle"
                                        alt="image">
                                </figure>
                            </div>
                        </td> --}}
                        <td class="text-right">
                            <div class="dropdown">
                                <a href="#" class="btn btn-floating" data-toggle="dropdown">
                                    <i class="ti-more-alt"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item"
                                        data-sidebar-target="#view-detail{{$file->id}}">View
                                        Details</a>
                                    @if ($file->is_private)
                                    <a href="#" class="dropdown-item"
                                        onclick="showSecretFormModalForView({{ $file->id }})">Preview</a>
                                    @else
                                    <a href="{{ route('files.preview', $file->id) }}" class="dropdown-item"
                                        target="_blank">Preview</a>
                                    @endif
                                    <a href="#" class="dropdown-item"
                                        onclick="showShareModal({{ $file->id }}, '{{ $file->original_name }}')">Share</a>
                                    @if ($file->is_private)
                                    <a href="#" class="dropdown-item"
                                        onclick="showSecretFormModalForDownload({{ $file->id }})">Download</a>
                                    @else
                                    <a href="{{ route('files.download', $file->id) }}"
                                        class="dropdown-item">Download</a>
                                    @endif
                                    @if (Auth::user()->role == 'admin')
                                    <a href="#" class="dropdown-item" data-toggle="modal"
                                        data-target="#privacyModal{{ $file->id }}">Set
                                        Privacy</a>
                                    <a href="#" class="dropdown-item"
                                        onclick="showRenameModal({{ $file->id }}, '{{
                                                                    remove_after_dot($file->original_name) }}')">Rename</a>
                                    <a href="#" class="dropdown-item"
                                        onclick="showDeleteModal({{ $file->id }}, '{{ $file->original_name }}', {{ $file->is_private }})">Delete</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- Privacy Modal -->
                    <div class="modal fade" id="privacyModal{{ $file->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static"
                        data-keyboard="false">
                        <div class="modal-dialog modal-dialog-center">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Update File
                                        Privacy</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('files.toggle.private', $file->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_private"
                                                id="isPrivate{{ $file->id }}" {{ $file->is_private ? 'checked' : ''
                                            }} onchange="toggleSecretCodeInput({{ $file->id }})">
                                            <label class="form-check-label" for="isPrivate{{ $file->id }}">
                                                Mark as Private
                                            </label>
                                        </div>

                                        <div class="mb-3 {{ $file->is_private ? '' : 'd-none' }}"
                                            id="secretCodeInputContainer{{ $file->id }}">
                                            <label for="secret_code" class="form-label">Secret Code (only for
                                                private
                                                files)</label>
                                            <input type="text" name="secret_code" class="form-control"
                                                placeholder="Enter Secret Code" @if($file->is_private) required
                                            @endif>
                                            <p class="text-info">Secret code should be at least 4 characters.</p>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Privacy</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
            {{ $results->withQueryString()->links() }}
        </div>
        @else
        <p>No files found for your search.</p>
        @endif
    </div>

    {{-- Rename Modal --}}
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalLabel">Rename File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="renameForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" name="new_name" class="form-control" placeholder="Enter new name"
                                id="newFileName">
                            <p class="form-text text-danger">Don't add the extension of the file. e.g:
                                <code>.jpg</code>, <code>.png</code>, <code>.pdf</code>, etc
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this file?</p>

                        <div class="form-group" id="secretCodeInputForDelete">
                            <label for="secret_code" class="form-label">Secret Code</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter Secret Code"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Share Modal --}}
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <form action="{{ route('files.share', $file->id) }}" method="POST"> --}}
                    <form action="" method="POST" id="shareForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Enter email address"
                                    required>
                                <small class="text-muted">Only users already registered can receive files.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Share</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showSecretFormModalForView" tabindex="-1" aria-labelledby="ViewModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewModalLabel">Enter Secret Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="viewForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="secret_code_for_download" class="form-label">Secret
                                Code</label>
                            <input type="password" name="secret_code" class="form-control"
                                placeholder="Enter Secret Code" required id="secret_code_for_download">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showSecretFormModalForDownload" tabindex="-1" aria-labelledby="downloadModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadModalLabel">Enter Secret Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="downloadForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="secret_code_for_view" class="form-label">Secret
                                Code</label>
                            <input type="password" name="secret_code" class="form-control"
                                placeholder="Enter Secret Code" required id="secret_code_for_view">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function showRenameModal(fileId, currentName) {
    const modal = new bootstrap.Modal(document.getElementById('renameModal'));
    // document.getElementById('renameModalLabel').innerText = `Rename File: ${currentName}`;
    document.getElementById('renameForm').action = `{{ route('files.rename', ':id') }}`.replace(':id', fileId);
    document.getElementById('newFileName').value = currentName;
    modal.show();
    }

    function showShareModal(fileId, fileName) {
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    document.getElementById('shareForm').action = `{{ route('files.share', ':id') }}`.replace(':id', fileId);
    document.getElementById('shareModalLabel').innerText = `Share File: ${fileName}`;
    modal.show();
    }

    function showDeleteModal(fileId, fileName, fileIsPrivate) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('deleteForm').action = `{{ route('files.destroy', ':id') }}`.replace(':id', fileId);
    document.getElementById('deleteModalLabel').innerText = `Delete File: ${fileName}`;

    if (fileIsPrivate) {
    document.getElementById('secretCodeInputForDelete').classList.remove('d-none');
    } else{
    document.getElementById('secretCodeInputForDelete').classList.add('d-none');
    }
    modal.show();
    }

    function showPrivacyModal(fileId) {
    const modal = new bootstrap.Modal(document.getElementById('privacyModal'));
    document.getElementById('privacyForm').action = `{{ route('files.toggle.private', ':id') }}`.replace(':id', fileId);
    // document.getElementById('deleteModalLabel').innerText = `Delete File: ${fileName}`;
    modal.show();
    }

    function toggleSecretCodeInput(fileId) {
    const secretCodeInputContainer = document.getElementById(`secretCodeInputContainer${fileId}`);
    secretCodeInputContainer.classList.toggle('d-none');
    }

    function showSecretFormModalForView(fileId) {
    const modal = new bootstrap.Modal(document.getElementById('showSecretFormModalForView'));
    document.getElementById('viewForm').action = `{{ route('files.private.view', ':id') }}`.replace(':id', fileId);
    modal.show();
    }

    function showSecretFormModalForDownload(fileId) {
    const modal = new bootstrap.Modal(document.getElementById('showSecretFormModalForDownload'));
    document.getElementById('downloadForm').action = `{{ route('files.private.download', ':id') }}`.replace(':id', fileId);
    modal.show();
    }
</script>
@endsection