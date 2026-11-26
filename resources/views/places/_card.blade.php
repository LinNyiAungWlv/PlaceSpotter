<div class="card h-100 shadow-sm">
    @include('places._photo', ['height' => 180])
    <div class="card-body">
        <span class="badge mb-2" style="background-color: {{ $place->category->color }}">{{ $place->category->name }}</span>
        @isset($place->distance)
            <span class="badge bg-light text-dark mb-2">📍 {{ number_format($place->distance, 1) }} km away</span>
        @endisset
        <h2 class="h5 card-title">
            <a href="{{ route('places.show', $place) }}" class="stretched-link text-decoration-none text-dark">{{ $place->name }}</a>
        </h2>
        <p class="card-text text-muted small">{{ Str::limit($place->description, 90) }}</p>
    </div>
    <div class="card-footer bg-white small text-muted">
        ❤️ {{ $place->likes_count }} · 💬 {{ $place->comments_count }} · by {{ $place->user->name }}
    </div>
</div>
