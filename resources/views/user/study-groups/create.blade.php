<div class="card shadow">
    <div class="card-header">
        <h1>Create Study Group</h1>
    </div>

    <div class="card-body">
        <form action="{{ route('user.study-groups.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter study group name">
                <p class="text-danger">{{ $errors->first('name') }}</p>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                    placeholder="Enter a brief description about the study group"></textarea>
                <p class="text-danger">{{ $errors->first('description') }}</p>
            </div>

            <button type="submit" class="btn btn-primary btn-lg d-block w-50 mx-auto">Create</button>
        </form>
    </div>
</div>