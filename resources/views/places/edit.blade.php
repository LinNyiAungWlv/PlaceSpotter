@extends('layouts.app')

@section('title', 'Edit '.$place->name.' - PlaceSpotter')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Edit place</h1>
                    <form method="POST" action="{{ route('places.update', $place) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @include('places._form')
                        <button type="submit" class="btn btn-primary w-100">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
