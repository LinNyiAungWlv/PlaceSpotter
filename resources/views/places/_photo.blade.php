@if ($place->photo)
    <img src="{{ asset('storage/'.$place->photo) }}" alt="{{ $place->name }}"
         class="card-img-top" style="height: {{ $height }}px; object-fit: cover;">
@else
    {{-- coloured box when there is no photo --}}
    <div class="card-img-top d-flex align-items-center justify-content-center text-white fw-bold"
         style="height: {{ $height }}px; background-color: {{ $place->category->color }};">
        {{ $place->category->name }}
    </div>
@endif
