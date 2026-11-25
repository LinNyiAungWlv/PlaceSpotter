@extends('layouts.app')

@section('title', 'Places - PlaceSpotter')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Places in Yangon</h1>
        @auth
            <a href="{{ route('places.create') }}" class="btn btn-success">+ Add place</a>
        @endauth
    </div>

    <div id="map" data-url="{{ route('places.mapData') }}" class="rounded shadow-sm mb-4" style="height: 400px;"></div>

    <div class="row g-3">
        @forelse ($places as $place)
            <div class="col-sm-6 col-lg-4">
                @include('places._card')
            </div>
        @empty
            <p class="text-muted">No places yet. Be the first to share one!</p>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/places-map.js') }}"></script>
@endpush
