@extends('layouts.app')

@section('title', 'Explore')

{{-- Leaflet is only loaded on pages that show a map --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
@endpush

@section('content')
    <h1 class="h3 mb-3">Explore places</h1>
    <p class="text-muted">
        Discover interesting places shared by other people – cafés, viewpoints, street art and more.
    </p>

    {{-- explore.js loads the pins from the JSON endpoint --}}
    <div id="map-error" class="alert alert-warning d-none" role="alert"></div>
    <div id="map" class="map-explore rounded shadow-sm mb-4" data-search-url="{{ route('places.search') }}"></div>

    {{-- list of places (rebuilt by JavaScript later for live search) --}}
    @if ($places->isEmpty())
        <p class="text-muted">No places yet.</p>
    @else
        <div class="row g-3">
            @foreach ($places as $place)
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        @if ($place->photo_url)
                            <img src="{{ $place->photo_url }}" class="card-img-top place-photo" alt="Photo of {{ $place->name }}">
                        @else
                            <div class="card-img-top place-photo place-photo-empty">
                                <i class="bi {{ $place->category->icon }}"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <span class="badge text-bg-light border mb-2">
                                <i class="bi {{ $place->category->icon }}"></i> {{ $place->category->name }}
                            </span>
                            <h2 class="h5 card-title">
                                {{-- stretched-link makes the whole card clickable --}}
                                <a href="{{ route('places.show', $place) }}" class="stretched-link text-reset text-decoration-none">{{ $place->name }}</a>
                            </h2>
                            <p class="card-text text-muted small">{{ Str::limit($place->description, 100) }}</p>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            by {{ $place->user->name }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/explore.js') }}"></script>
@endpush
