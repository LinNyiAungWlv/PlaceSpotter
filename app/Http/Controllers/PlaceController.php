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
        // TODO: save the place to the database
        return back()
            ->withInput()
            ->with('status', 'Looks good! Saving places is coming next.');
    }
}
