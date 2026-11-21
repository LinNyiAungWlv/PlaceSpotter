@extends('layouts.app')

@section('title', 'Explore')

{{-- Leaflet is only loaded on pages that show a map --}}
@include('partials.leaflet')

@section('content')
    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
        <h1 class="h3 mb-0">Explore places</h1>
        {{-- explore.js asks for the GPS position and shows the places around it --}}
        <button type="button" id="near-me" class="btn btn-outline-secondary btn-sm text-nowrap">
            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
            <i class="bi bi-geo-alt"></i> Near me
        </button>
    </div>
    <p class="text-muted">
        Discover interesting places shared by other people – cafés, viewpoints, street art and more.
    </p>

    {{-- explore.js loads the pins from the JSON endpoints --}}
    <div id="map-error" class="alert alert-warning d-none" role="alert"></div>
    <div id="map" class="map-explore rounded shadow-sm mb-4"
         data-search-url="{{ route('places.search') }}"
         data-nearby-url="{{ route('places.nearby') }}"></div>

    {{-- after "Near me": how many places were found, and a link back to all of them --}}
    <div id="list-status" class="small mb-3 d-none" role="status">
        <span id="list-status-text"></span>
        <button type="button" id="show-all" class="btn btn-link btn-sm p-0 align-baseline">Show all places</button>
    </div>

    {{-- list of places, explore.js rebuilds it for "Near me" (and later for live search) --}}
    @if ($places->isEmpty())
        <p class="text-muted">No places yet.</p>
    @endif

    {{-- the same card is built in explore.js (buildCard), keep the two the same --}}
    <div id="place-list" class="row g-3">
        @foreach ($places as $place)
            <div class="col-sm-6 col-lg-4">
                <div class="card place-card h-100 shadow-sm">
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
@endsection

@push('scripts')
    <script src="{{ asset('js/explore.js') }}"></script>
@endpush
