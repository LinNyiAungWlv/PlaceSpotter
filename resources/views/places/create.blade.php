@extends('layouts.app')

@section('title', 'Add a place - PlaceSpotter')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Share a place</h1>
                    <form method="POST" action="{{ route('places.store') }}" enctype="multipart/form-data">
                        @include('places._form')
                        <button type="submit" class="btn btn-success w-100">Share place</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
