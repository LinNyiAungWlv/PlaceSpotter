<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Place;

// JSON endpoints used by the JavaScript on the explore page (map pins, search)
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
}
