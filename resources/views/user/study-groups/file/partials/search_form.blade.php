<form method="GET" action="{{ route('files.search') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by filename...">
    </div>

    <div class="col-md-2">
        <select name="type" class="custom-select">
            <option value="">All Types</option>
            <option value="image" {{ request('type')=='image' ? 'selected' : '' }}>Images</option>
            <option value="document" {{ request('type')=='document' ? 'selected' : '' }}>Documents</option>
            <option value="video" {{ request('type')=='video' ? 'selected' : '' }}>Videos</option>
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"
            placeholder="From Date">
    </div>

    <div class="col-md-2">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="To Date">
    </div>

    <div class="col-md-3 d-flex">
        <button type="submit" class="btn btn-primary me-2">Search</button>
        <a href="{{ route('files.search') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>