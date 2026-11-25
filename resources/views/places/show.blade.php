@extends('layouts.app')

@section('title', $place->name.' - PlaceSpotter')

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
                    <p class="small text-muted mb-2">📍 {{ $place->latitude }}, {{ $place->longitude }}</p>
                    <p class="mb-0">❤️ {{ $place->likes_count }} likes · 💬 {{ $place->comments_count }} comments</p>
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
