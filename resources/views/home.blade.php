@extends('layouts.app')

@section('title', 'Explore')

@section('content')
    <h1 class="h3 mb-3">Explore places</h1>
    <p class="text-muted">
        Discover interesting places shared by other people – cafés, viewpoints, street art and more.
    </p>

    {{-- simple list for now, the map comes later --}}
    @if ($places->isEmpty())
        <p class="text-muted">No places yet.</p>
    @else
        <div class="row g-3">
            @foreach ($places as $place)
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <span class="badge text-bg-light border mb-2">
                                <i class="bi {{ $place->category->icon }}"></i> {{ $place->category->name }}
                            </span>
                            <h2 class="h5 card-title">{{ $place->name }}</h2>
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
