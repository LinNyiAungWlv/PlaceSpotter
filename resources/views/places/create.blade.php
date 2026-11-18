@extends('layouts.app')

@section('title', 'Add a place')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">Add a place</h1>

                <form method="POST" action="{{ route('places.store') }}" novalidate>
                    @csrf

                    @include('places._form')

                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-plus-lg"></i> Add place
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-link text-muted">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
