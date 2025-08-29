@extends('layouts.user_layout')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between">
        <h2 class="mb-4">📁 {{ $title }}</h2>
        <a href="#" class="files-toggler">
            <i class="ti-menu"></i>
        </a>
    </div>

    @include('user.study-groups.file.create')

    <div class="row">
        <div class="col-xl-12">
            <div class="content-title mt-4">
                <h4>All Files</h4>
            </div>
            <div class="d-md-flex justify-content-between mb-4">
                <div id="file-actions" class="d-none">
                    <ul class="list-inline">
                        <li class="list-inline-item mb-0">
                            <a href="#" class="btn btn-outline-light" data-toggle="tooltip" title="Move">
                                <i class="ti-arrow-top-right"></i>
                            </a>
                        </li>
                        <li class="list-inline-item mb-0">
                            <a href="#" class="btn btn-outline-light" data-toggle="tooltip" title="Download">
                                <i class="ti-download"></i>
                            </a>
                        </li>
                        <li class="list-inline-item mb-0">
                            <a href="#" class="btn btn-outline-danger" data-toggle="tooltip" title="Delete">
                                <i class="ti-trash"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table id="example2" class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            {{-- <th>Status</th> --}}
                            <th>Added on</th>
                            <th>Modified</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                        <tr>
                            <td>
                                <a href="{{ route('user.study-groups.files.preview', [$file->study_group_id, $file->id]) }}"
                                    class="d-flex align-items-center" target="_blank" title="preview">
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
                            {{-- <td>
                                @if ($file->is_private)
                                <span class="badge bg-danger">Private</span>
                                @else
                                <span class="badge bg-success">Public</span>
                                @endif
                            </td> --}}
                            <td>{{ date('M d, Y H:iA', strtotime($file->created_at)) }}</td>
                            <td>{{ date('M d, Y H:iA', strtotime($file->updated_at)) }}</td>

                            <td class="text-right">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-floating" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        {{-- <a href="#" class="dropdown-item"
                                            data-sidebar-target="#view-detail{{$file->id}}">View
                                            Details</a> --}}
                                        <a href="{{ route('user.study-groups.files.preview', [$file->study_group_id, $file->id]) }}"
                                            class="dropdown-item" target="_blank">Preview</a>
                                        <a href="#" class="dropdown-item"
                                            onclick="showShareModal({{ $file->study_group_id }}, {{ $file->id }}, '{{ $file->original_name }}')">Share</a>

                                        <a href="{{ route('user.study-groups.files.download', [$file->study_group_id, $file->id]) }}"
                                            class="dropdown-item">Download</a>
                                        @if ($file->is_owner())
                                        {{-- <a href="#" class="dropdown-item" data-toggle="modal"
                                            data-target="#privacyModal{{ $file->id }}">Set Privacy</a> --}}
                                        <a href="#" class="dropdown-item" onclick="showRenameModal({{ $file->study_group_id }}, {{ $file->id }}, '{{
                                                remove_after_dot($file->original_name) }}')">Rename</a>
                                        <a href="#" class="dropdown-item"
                                            onclick="showDeleteModal({{ $file->study_group_id }}, {{ $file->id }}, '{{ $file->original_name }}')">Delete</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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

                        {{-- <div class="form-group" id="secretCodeInputForDelete">
                            <label for="secret_code" class="form-label">Secret Code</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter Secret Code"
                                required>
                        </div> --}}
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
                                <select name="email" id="email" class="form-control" required>
                                    <option value="">Select a member</option>
                                    @foreach ($studyGroup->members as $member)
                                    @if ($member->email == auth()->user()->email)
                                    @continue
                                    @endif
                                    <option value="{{ $member->email }}">{{ $member->email }}</option>
                                    @endforeach
                                </select>
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

</div>
@endsection

@section('scripts')
<script>
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("fileInput");
    const previewContainer = document.getElementById("preview");

    dropArea.addEventListener("click", () => fileInput.click());

    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("border-primary");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("border-primary");
    });

    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("border-primary");
        const files = e.dataTransfer.files;
        console.log(files);
        fileInput.files = files;
        handlePreview(files);
    });

    fileInput.addEventListener("change", () => {
        handlePreview(fileInput.files);
    });

    function handlePreview(files) {
        previewContainer.innerHTML = "";
        Array.from(files).forEach(file => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const col = document.createElement("div");
                col.className = "col-md-3 mb-3";

                if (file.type.startsWith("image/")) {
                    col.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded">`;
                } else if (file.type === "application/pdf") {
                    col.innerHTML = `<iframe src="${e.target.result}" class="w-100" style="height: 200px;"></iframe>`;
                } else {
                    col.innerHTML = `<div class="border p-2 rounded">${file.name}</div>`;
                }

                previewContainer.appendChild(col);
            };

            reader.readAsDataURL(file);
        });
    }

    function showRenameModal(groupId, fileId, currentName) {
        const modal = new bootstrap.Modal(document.getElementById('renameModal'));
        // document.getElementById('renameModalLabel').innerText = `Rename File: ${currentName}`;
        document.getElementById('renameForm').action = `{{ route('user.study-groups.files.rename', [':group_id', ':id']) }}`.replace(':group_id', groupId).replace(':id', fileId);
        document.getElementById('newFileName').value = currentName;
        modal.show();
    }

    function showShareModal(groupId, fileId, fileName) {
        const modal = new bootstrap.Modal(document.getElementById('shareModal'));
        document.getElementById('shareForm').action = `{{ route('user.study-groups.files.share', [':group_id', ':id']) }}`.replace(':group_id', groupId).replace(':id', fileId);
        document.getElementById('shareModalLabel').innerText = `Share File: ${fileName}`;
        modal.show();
    }

    function showDeleteModal(groupId, fileId, fileName) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('deleteForm').action = `{{ route('user.study-groups.files.destroy', [':group_id', ':id']) }}`.replace(':group_id', groupId).replace(':id', fileId);
        document.getElementById('deleteModalLabel').innerText = `Delete File: ${fileName}`;
        modal.show();
    }
</script>
@endsection