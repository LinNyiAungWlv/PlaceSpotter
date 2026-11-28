@extends('layouts.app')

@section('content')
    <div class="text-center py-4 py-md-5">
        <h1 class="fw-bold">Discover places in Yangon</h1>
        <p class="lead text-muted">Share cafés, viewpoints, street art and hidden gems with others.</p>
        <a href="{{ route('places.index') }}" class="btn btn-success btn-lg mb-2">Explore {{ $placeCount }} places</a>
        @guest
            <a href="{{ route('register') }}" class="btn btn-outline-success btn-lg mb-2">Join PlaceSpotter</a>
        @endguest
    </div>

    <div class="row text-center g-3 mb-5">
        <div class="col-6 col-md-3"><div class="fs-2">🗺️</div><div class="small">Explore on a map</div></div>
        <div class="col-6 col-md-3"><div class="fs-2">📍</div><div class="small">Find places near you</div></div>
        <div class="col-6 col-md-3"><div class="fs-2">☀️</div><div class="small">Live weather tips</div></div>
        <div class="col-6 col-md-3"><div class="fs-2">❤️</div><div class="small">Like and comment</div></div>
    </div>

    @if ($latestPlaces->isNotEmpty())
        <h2 class="h4 mb-3">Latest places</h2>
        <div class="row g-3">
            @foreach ($latestPlaces as $place)
                <div class="col-sm-6 col-lg-4">
                    @include('places._card')
                </div>
            @endforeach
        </div>
    @endif
@endsection
