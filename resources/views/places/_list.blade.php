@forelse ($places as $place)
    <div class="col-sm-6 col-lg-4">
        @include('places._card')
    </div>
@empty
    <p class="text-muted">No places found.</p>
@endforelse
