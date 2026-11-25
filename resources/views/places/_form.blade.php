@csrf

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $place->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Category</label>
    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">Choose a category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="4"
              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $place->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-6 mb-3">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $place->latitude ?? '') }}"
               class="form-control @error('latitude') is-invalid @enderror" required>
        @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-6 mb-3">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $place->longitude ?? '') }}"
               class="form-control @error('longitude') is-invalid @enderror" required>
        @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="photo" class="form-label">Photo</label>
    <input type="file" id="photo" name="photo" accept="image/*"
           class="form-control @error('photo') is-invalid @enderror">
    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @isset($place->photo)
        <div class="form-text">Leave empty to keep the current photo.</div>
    @endisset
</div>
