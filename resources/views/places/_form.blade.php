{{-- shared fields for the add and edit place forms --}}
<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $place->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Category</label>
    <select id="category_id" name="category_id"
            class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">Choose a category…</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $place->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="4"
              class="form-control @error('description') is-invalid @enderror"
              maxlength="2000" required>{{ old('description', $place->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @else
        <div class="form-text">What makes this place worth a visit?</div>
    @enderror
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label for="photo" class="form-label mb-0">Photo</label>
        {{-- only shown on phones (place-form.js), opens the back camera straight away --}}
        <button type="button" id="take-photo" class="btn btn-outline-secondary btn-sm d-none">
            <i class="bi bi-camera"></i> Take photo
        </button>
    </div>

    {{-- when editing, show the current photo --}}
    @if (isset($place) && $place->photo_url)
        <div id="current-photo" class="mb-2">
            <img src="{{ $place->photo_url }}" alt="Current photo" class="photo-preview rounded border">
        </div>
    @endif

    {{-- place-form.js shows the chosen photo here before the form is saved --}}
    <div id="photo-preview" class="mb-2 d-none">
        <img alt="Preview of the chosen photo" class="photo-preview rounded border">
        <div class="d-flex align-items-center gap-2 mt-1 small">
            <span id="photo-info" class="text-muted text-truncate"></span>
            <button type="button" id="remove-photo" class="btn btn-link btn-sm p-0 text-danger">Remove</button>
        </div>
    </div>

    <input type="file" id="photo" name="photo" accept="image/*"
           class="form-control @error('photo') is-invalid @enderror">
    {{-- no name, so it is never sent: its photo is copied into the input above --}}
    <input type="file" id="camera-input" accept="image/*" capture="environment" class="d-none">
    <div id="photo-error" class="small text-danger mt-1 d-none"></div>
    @error('photo')
        <div class="invalid-feedback">{{ $message }}</div>
    @else
        <div class="form-text">
            An image file (e.g. JPG or PNG), up to 8 MB. Big photos are made smaller before upload.
            @isset($place) Leave empty to keep the current photo. @endisset
        </div>
    @enderror
</div>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label mb-0">Location</label>
        {{-- type="button" so it does not submit the form --}}
        <button type="button" id="use-location" class="btn btn-outline-secondary btn-sm">
            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
            <i class="bi bi-crosshair"></i> Use my location
        </button>
    </div>
    <div class="form-text mt-0 mb-2">Click the map to set the location, or drag the pin.</div>
    <div id="location-status" class="small mb-2 d-none" role="status"></div>

    {{-- place-form.js keeps this map and the two inputs below in sync --}}
    <div id="location-map" class="map-form rounded border mb-2"></div>

    <div class="row g-2">
        <div class="col-6">
            <label for="latitude" class="form-label small text-muted mb-1">Latitude</label>
            <input type="number" id="latitude" name="latitude" step="any"
                   value="{{ old('latitude', $place->latitude ?? '') }}"
                   class="form-control form-control-sm @error('latitude') is-invalid @enderror" required>
            @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6">
            <label for="longitude" class="form-label small text-muted mb-1">Longitude</label>
            <input type="number" id="longitude" name="longitude" step="any"
                   value="{{ old('longitude', $place->longitude ?? '') }}"
                   class="form-control form-control-sm @error('longitude') is-invalid @enderror" required>
            @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
