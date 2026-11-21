<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    // places added by the logged in user, newest first
    public function mine(Request $request)
    {
        $places = $request->user()->places()->with('category')->latest()->get();

        return view('places.mine', compact('places'));
    }

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

    public function edit(Place $place)
    {
        // 403 if the logged in user is not the owner (see PlacePolicy)
        Gate::authorize('update', $place);

        $categories = Category::orderBy('name')->get();

        return view('places.edit', compact('place', 'categories'));
    }

    // the owner check for update happens in PlaceRequest::authorize()
    public function update(PlaceRequest $request, Place $place)
    {
        $data = $request->safe()->except('photo');

        // only replace the photo if a new one was uploaded
        if ($request->hasFile('photo')) {
            $this->deletePhoto($place);
            $data['photo_path'] = $request->file('photo')->store('places', 'public');
        }

        $place->update($data);

        return redirect()->route('places.show', $place)->with('status', 'Place updated!');
    }

    public function destroy(Place $place)
    {
        Gate::authorize('delete', $place);

        // likes and comments are deleted by the database (cascadeOnDelete)
        $this->deletePhoto($place);
        $place->delete();

        return redirect()->route('places.mine')->with('status', 'Place deleted.');
    }

    // removes the photo file so old uploads don't pile up in storage
    private function deletePhoto(Place $place): void
    {
        if ($place->photo_path) {
            Storage::disk('public')->delete($place->photo_path);
        }
    }
}
