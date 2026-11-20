<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Category;
use App\Models\Place;

class PlaceController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('places.create', compact('categories'));
    }

    public function store(PlaceRequest $request)
    {
        // PlaceRequest has already validated the input at this point
        $data = $request->safe()->except('photo');

        // saved to storage/app/public/places with a random file name
        $data['photo_path'] = $request->file('photo')->store('places', 'public');

        // creating through the relationship sets user_id to the logged in user
        $place = $request->user()->places()->create($data);

        return redirect()->route('places.show', $place)->with('status', 'Place added!');
    }

    // Laravel finds the place from the id in the URL, or shows a 404
    public function show(Place $place)
    {
        $place->load(['category', 'user']);

        return view('places.show', compact('place'));
    }
}
