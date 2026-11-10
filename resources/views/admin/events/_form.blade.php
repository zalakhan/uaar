<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $event->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $event->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="event_type" class="form-label">Event type</label>
        <input type="text" name="event_type" id="event_type" class="form-control" value="{{ old('event_type', $event->event_type ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="department_id" class="form-label">Organizing department</label>
        <select name="department_id" id="department_id" class="form-select">
            <option value="">— None —</option>
            @foreach ($departments as $id => $name)
                <option value="{{ $id }}" @selected((string) old('department_id', $event->department_id ?? '') === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="event_date" class="form-label">Event date</label>
        <input type="date" name="event_date" id="event_date" class="form-control"
               value="{{ old('event_date', isset($event) && $event->event_date ? $event->event_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="registration_deadline" class="form-label">Registration deadline</label>
        <input type="date" name="registration_deadline" id="registration_deadline" class="form-control"
               value="{{ old('registration_deadline', isset($event) && $event->registration_deadline ? $event->registration_deadline->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="venue" class="form-label">Venue</label>
        <input type="text" name="venue" id="venue" class="form-control" value="{{ old('venue', $event->venue ?? '') }}">
    </div>
</div>
