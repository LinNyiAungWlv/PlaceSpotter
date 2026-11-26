@extends('layouts.app')

@section('title', $place->name.' - PlaceSpotter')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                @include('places._photo', ['height' => 320])
                <div class="card-body p-4">
                    <span class="badge mb-2" style="background-color: {{ $place->category->color }}">{{ $place->category->name }}</span>
                    <h1 class="h3">{{ $place->name }}</h1>
                    <p class="text-muted small">Shared by {{ $place->user->name }} · {{ $place->created_at->diffForHumans() }}</p>
                    <p>{{ $place->description }}</p>

                    <div id="weather" class="alert alert-secondary" data-url="{{ route('places.weather', $place) }}">
                        Loading weather...
                    </div>

                    <div id="place-map" class="rounded mb-3" style="height: 220px;"
                         data-lat="{{ $place->latitude }}" data-lng="{{ $place->longitude }}"></div>
                    @auth
                        <button type="button" id="like-button" data-url="{{ route('places.like', $place) }}"
                                class="btn {{ $liked ? 'btn-danger' : 'btn-outline-danger' }}">
                            ❤️ <span id="like-count">{{ $place->likes_count }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-danger">❤️ {{ $place->likes_count }} · Log in to like</a>
                    @endauth
                </div>

                <div class="card-body border-top p-4">
                    <h2 class="h5">💬 Comments (<span id="comment-count">{{ $place->comments->count() }}</span>)</h2>

                    @auth
                        <form id="comment-form" data-url="{{ route('comments.store', $place) }}" class="mb-3">
                            <textarea name="body" id="comment-body" rows="2" class="form-control mb-2"
                                      placeholder="Write a comment..." maxlength="1000"></textarea>
                            <div id="comment-error" class="text-danger small mb-2"></div>
                            <button type="submit" class="btn btn-success btn-sm">Post comment</button>
                        </form>
                    @else
                        <p class="small"><a href="{{ route('login') }}">Log in</a> to leave a comment.</p>
                    @endauth

                    <div id="comment-list">
                        @foreach ($place->comments as $comment)
                            @include('comments._comment')
                        @endforeach
                    </div>
                </div>

                @can('update', $place)
                    <div class="card-footer bg-white d-flex gap-2">
                        <a href="{{ route('places.edit', $place) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('places.destroy', $place) }}"
                              onsubmit="return confirm('Delete this place?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                        </form>
                    </div>
                @endcan
            </div>

            <a href="{{ route('places.index') }}" class="btn btn-link mt-2 px-0">&larr; Back to all places</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // small map with one pin for this place
        const box = document.getElementById('place-map');
        const spot = [box.dataset.lat, box.dataset.lng];
        const placeMap = L.map(box).setView(spot, 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(placeMap);
        L.marker(spot).addTo(placeMap);
    </script>
    <script src="{{ asset('js/weather.js') }}"></script>
    <script src="{{ asset('js/place-interactions.js') }}"></script>
@endpush
