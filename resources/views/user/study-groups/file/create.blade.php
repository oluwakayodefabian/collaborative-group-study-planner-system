{{-- @extends('layouts.user_layout')

@section('content')--}}
<div class="container py-4 shadow">
    {{-- <h2 class="mb-4">📁 File Upload & Management</h2> --}}

    {{-- File Upload Form --}}
    <form id="uploadForm" method="POST" enctype="multipart/form-data"
        action="{{ route('user.study-groups.files.store', $studyGroup) }}">
        @csrf
        <div class="alert alert-danger">
            <p>Maximum upload size: 10 MB</p>
            <p>Allowed file types: .pdf, .doc, .docx, .jpg, .jpeg, .png</p>
        </div>
        <div class="border-2 border-dashed rounded p-4 text-center bg-light" id="drop-area" style="cursor: pointer;">
            <p>Drag & drop files here or click to select</p>
            <input type="file" id="fileInput" name="files[]" multiple hidden>
        </div>

        <div id="preview" class="row mt-3"></div>

        <button class="btn btn-primary mt-3" type="submit">Upload</button>
    </form>

    {{-- Uploaded Files List --}}
    {{-- <h4>📂 Uploaded Files</h4>
    <div id="uploadedFiles">
        @foreach ($files as $file)
        <div class="card my-2 p-2">
            <div class="d-flex justify-content-between align-items-center">
                <span>{{ $file->original_name }}</span>
                <div>
                    <a href="{{ route('files.preview', $file->id) }}" class="btn btn-sm btn-secondary"
                        target="_blank">Preview</a>
                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-success">Download</a>
                </div>
            </div>
        </div>
        @endforeach
    </div> --}}
</div>
{{-- @endsection --}}