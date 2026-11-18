<?php

namespace App\Http\Controllers;

use App\Models\Place;

class HomeController extends Controller
{
    public function index()
    {
        // with() loads categories and users in 2 extra queries instead of one per place
        $places = Place::with(['category', 'user'])->latest()->get();

        return view('home', compact('places'));
    }
}
