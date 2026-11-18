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

<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="number" id="latitude" name="latitude" step="any"
               value="{{ old('latitude', $place->latitude ?? '') }}"
               class="form-control @error('latitude') is-invalid @enderror" required>
        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-6">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="number" id="longitude" name="longitude" step="any"
               value="{{ old('longitude', $place->longitude ?? '') }}"
               class="form-control @error('longitude') is-invalid @enderror" required>
        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
