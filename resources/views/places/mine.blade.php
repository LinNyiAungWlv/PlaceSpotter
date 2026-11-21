@extends('layouts.app')

@section('title', 'My places')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">My places <span class="text-muted fs-5">({{ $places->count() }})</span></h1>
    <a href="{{ route('places.create') }}" class="btn btn-brand">
        <i class="bi bi-plus-lg"></i> Add place
    </a>
</div>

@if ($places->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-geo-alt display-5 text-muted"></i>
            <p class="text-muted mt-3">You haven't added any places yet.</p>
            <a href="{{ route('places.create') }}" class="btn btn-brand">Add your first place</a>
        </div>
    </div>
@else
    <div class="list-group shadow-sm">
        @foreach ($places as $place)
            <div class="list-group-item d-flex flex-column flex-sm-row gap-3 align-items-sm-center py-3">
                <div class="d-flex gap-3 align-items-center flex-grow-1">
                    @if ($place->photo_url)
                        <img src="{{ $place->photo_url }}" alt="Photo of {{ $place->name }}" class="place-thumb rounded">
                    @else
                        <div class="place-thumb place-photo-empty rounded">
                            <i class="bi {{ $place->category->icon }}"></i>
                        </div>
                    @endif
                    <div>
                        <a href="{{ route('places.show', $place) }}" class="fw-semibold text-decoration-none">{{ $place->name }}</a>
                        <div class="small text-muted">
                            <i class="bi {{ $place->category->icon }}"></i> {{ $place->category->name }}
                            &middot; added {{ $place->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('places.edit', $place) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @include('places._delete_button')
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
