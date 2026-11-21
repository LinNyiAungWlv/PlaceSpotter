@extends('layouts.app')

@section('title', 'Edit ' . $place->name)

@include('partials.leaflet')

@push('scripts')
    <script src="{{ asset('js/place-form.js') }}"></script>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">Edit place</h1>

                <form method="POST" action="{{ route('places.update', $place) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    {{-- HTML forms can only POST, this hidden field tells Laravel it is a PUT --}}
                    @method('PUT')

                    @include('places._form')

                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-check-lg"></i> Save changes
                    </button>
                    <a href="{{ route('places.show', $place) }}" class="btn btn-link text-muted">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
