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
        // PlaceRequest has already validated the input at this point.
        // Creating through the relationship sets user_id to the logged in user.
        $request->user()->places()->create($request->validated());

        return redirect()->route('home')->with('status', 'Place added!');
    }
}
