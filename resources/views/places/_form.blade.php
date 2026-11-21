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
    <label for="photo" class="form-label">Photo</label>

    {{-- when editing, show the current photo --}}
    @if (isset($place) && $place->photo_url)
        <div class="mb-2">
            <img src="{{ $place->photo_url }}" alt="Current photo" class="rounded border" style="max-height: 120px;">
        </div>
    @endif

    <input type="file" id="photo" name="photo" accept="image/*"
           class="form-control @error('photo') is-invalid @enderror">
    @error('photo')
        <div class="invalid-feedback">{{ $message }}</div>
    @else
        <div class="form-text">
            An image file (e.g. JPG or PNG), up to 8 MB.
            @isset($place) Leave empty to keep the current photo. @endisset
        </div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label mb-1">Location</label>
    <div class="form-text mt-0 mb-2">Click the map to set the location, or drag the pin.</div>

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
