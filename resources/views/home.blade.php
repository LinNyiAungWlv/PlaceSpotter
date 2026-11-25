@extends('layouts.app')

@section('content')
    <div class="text-center py-5">
        <h1 class="fw-bold">Discover places in Yangon</h1>
        <p class="lead text-muted">Share cafés, viewpoints, street art and hidden gems with others.</p>
        @guest
            <a href="{{ route('register') }}" class="btn btn-success btn-lg">Join PlaceSpotter</a>
        @endguest
    </div>
@endsection
