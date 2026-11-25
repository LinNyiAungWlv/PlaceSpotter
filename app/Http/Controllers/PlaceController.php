<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::with('category', 'user')->withCount('likes', 'comments')->latest()->get();

        return view('places.index', compact('places'));
    }

    // JSON list of places for the map pins
    public function mapData()
    {
        $places = Place::with('category')->latest()->get()->map(fn ($place) => [
            'id' => $place->id,
            'name' => $place->name,
            'category' => $place->category->name,
            'color' => $place->category->color,
            'lat' => (float) $place->latitude,
            'lng' => (float) $place->longitude,
            'url' => route('places.show', $place),
        ]);

        return response()->json($places);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('places.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePlace($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('places', 'public');
        }

        $place = $request->user()->places()->create($data);

        return redirect()->route('places.show', $place)->with('success', 'Place shared!');
    }

    public function show(Place $place)
    {
        $place->load('category', 'user')->loadCount('likes', 'comments');

        return view('places.show', compact('place'));
    }

    public function edit(Place $place)
    {
        Gate::authorize('update', $place);
        $categories = Category::orderBy('name')->get();

        return view('places.edit', compact('place', 'categories'));
    }

    public function update(Request $request, Place $place)
    {
        Gate::authorize('update', $place);
        $data = $this->validatePlace($request);

        if ($request->hasFile('photo')) {
            // replace the old photo with the new one
            if ($place->photo) {
                Storage::disk('public')->delete($place->photo);
            }
            $data['photo'] = $request->file('photo')->store('places', 'public');
        }

        $place->update($data);

        return redirect()->route('places.show', $place)->with('success', 'Place updated.');
    }

    public function destroy(Place $place)
    {
        Gate::authorize('delete', $place);

        if ($place->photo) {
            Storage::disk('public')->delete($place->photo);
        }
        $place->delete();

        return redirect()->route('places.index')->with('success', 'Place deleted.');
    }

    private function validatePlace(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:2000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|max:4096',
        ]);
    }
}
