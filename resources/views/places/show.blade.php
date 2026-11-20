@extends('layouts.app')

@section('title', $place->name)

@section('content')
<a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
    <i class="bi bi-arrow-left"></i> Back to explore
</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            @if ($place->photo_url)
                <img src="{{ $place->photo_url }}" class="card-img-top place-photo place-photo-lg" alt="Photo of {{ $place->name }}">
            @else
                <div class="card-img-top place-photo place-photo-lg place-photo-empty">
                    <i class="bi {{ $place->category->icon }}"></i>
                </div>
            @endif

            <div class="card-body p-4">
                <span class="badge text-bg-light border mb-2">
                    <i class="bi {{ $place->category->icon }}"></i> {{ $place->category->name }}
                </span>
                <h1 class="h3">{{ $place->name }}</h1>
                <p class="text-muted small">
                    Added by {{ $place->user->name }} &middot;
                    <span title="{{ $place->created_at->format('j M Y, H:i') }}">{{ $place->created_at->diffForHumans() }}</span>
                </p>

                {{-- pre-line keeps the line breaks the user typed, {{ }} escapes any HTML --}}
                <p class="mb-0 text-pre-line">{{ $place->description }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6"><i class="bi bi-geo-alt"></i> Location</h2>
                <p class="small text-muted mb-2">
                    {{ number_format($place->latitude, 5) }}, {{ number_format($place->longitude, 5) }}
                </p>
                <a href="https://www.openstreetmap.org/?mlat={{ $place->latitude }}&mlon={{ $place->longitude }}#map=17/{{ $place->latitude }}/{{ $place->longitude }}"
                   class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-up-right"></i> Open in OpenStreetMap
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
