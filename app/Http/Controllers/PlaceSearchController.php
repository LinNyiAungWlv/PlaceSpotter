<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

// JSON endpoints used by the JavaScript on the explore page (map pins, search, near me)
class PlaceSearchController extends Controller
{
    public function index()
    {
        // with() and withCount() load everything in a few queries instead of one per place
        $places = Place::with(['category', 'user'])
            ->withCount('likes')
            ->latest()
            ->limit(50)
            ->get();

        return PlaceResource::collection($places);
    }

    // places around a point (the user's GPS position), nearest first, with the distance in km
    public function nearby(Request $request)
    {
        // bad input gives a 422 JSON error when the request asks for JSON (fetch sends Accept: application/json)
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'km' => ['nullable', 'numeric', 'between:0.1,50'],
        ]);

        $places = Place::nearby($data['lat'], $data['lng'], $data['km'] ?? 5)
            ->with(['category', 'user'])
            ->withCount('likes')
            ->limit(50)
            ->get();

        return PlaceResource::collection($places);
    }
}
