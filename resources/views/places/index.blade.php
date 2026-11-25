@extends('layouts.app')

@section('title', 'Places - PlaceSpotter')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Places in Yangon</h1>
        @auth
            <a href="{{ route('places.create') }}" class="btn btn-success">+ Add place</a>
        @endauth
    </div>

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
