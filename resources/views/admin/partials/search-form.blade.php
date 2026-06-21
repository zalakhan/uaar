<form method="GET" action="{{ $action }}" class="row g-2 mb-4">
    <div class="col-md-6">
        <input type="search" name="search" class="form-control" placeholder="{{ $placeholder ?? 'Search...' }}"
               value="{{ request('search') }}" maxlength="255">
    </div>
    <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if (request('search'))
            <a href="{{ $action }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>
