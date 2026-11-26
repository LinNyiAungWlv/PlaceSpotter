<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $places = $this->filteredPlaces($request);
        $categories = Category::orderBy('name')->get();

        return view('places.index', compact('places', 'categories'));
    }

    // AJAX: returns the card HTML and the map pins for the current filter
    public function search(Request $request)
    {
        $places = $this->filteredPlaces($request);

        return response()->json([
            'count' => $places->count(),
            'html' => view('places._list', compact('places'))->render(),
            'pins' => $places->map(fn ($place) => [
                'id' => $place->id,
                'name' => $place->name,
                'category' => $place->category->name,
                'color' => $place->category->color,
                'lat' => (float) $place->latitude,
                'lng' => (float) $place->longitude,
                'url' => route('places.show', $place),
            ]),
        ]);
    }

    private function filteredPlaces(Request $request)
    {
        return Place::with('category', 'user')
            ->withCount('likes', 'comments')
            ->when($request->category, fn ($query, $category) => $query->where('category_id', $category))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled(['lat', 'lng']),
                fn ($query) => $this->nearby($query, (float) $request->lat, (float) $request->lng),
                fn ($query) => $query->latest()
            )
            ->get();
    }

    // "Near me": places within 10 km, closest first (Haversine formula, distance in km)
    private function nearby($query, float $lat, float $lng)
    {
        return $query
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', 10)
            ->orderBy('distance');
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
        $place->load(['category', 'user', 'comments' => fn ($query) => $query->with('user')->latest()])
            ->loadCount('likes');

        $liked = auth()->check() && $place->likes()->where('user_id', auth()->id())->exists();

        return view('places.show', compact('place', 'liked'));
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
