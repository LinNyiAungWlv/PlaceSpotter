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

    <input type="search" id="search" class="form-control mb-2" placeholder="Search places..." autocomplete="off">

    <div id="category-filter" class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="btn btn-sm btn-dark" data-category="">All</button>
        @foreach ($categories as $category)
            <button type="button" class="btn btn-sm btn-outline-dark" data-category="{{ $category->id }}">{{ $category->name }}</button>
        @endforeach
    </div>

    <div id="map" data-url="{{ route('places.search') }}" class="rounded shadow-sm mb-2" style="height: 400px;"></div>
    <p class="small text-muted mb-3"><span id="result-count">{{ $places->count() }}</span> places found</p>

    <div id="place-list" class="row g-3">
        @include('places._list')
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/places-map.js') }}"></script>
@endpush
