<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Category;

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
        $request->user()->places()->create($data);

        return redirect()->route('home')->with('status', 'Place added!');
    }
}
