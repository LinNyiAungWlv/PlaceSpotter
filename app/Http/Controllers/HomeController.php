<?php

namespace App\Http\Controllers;

use App\Models\Place;

class HomeController extends Controller
{
    public function index()
    {
        $latestPlaces = Place::with('category', 'user')->withCount('likes', 'comments')->latest()->take(3)->get();
        $placeCount = Place::count();

        return view('home', compact('latestPlaces', 'placeCount'));
    }
}
