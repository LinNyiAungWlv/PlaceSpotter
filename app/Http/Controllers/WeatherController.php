<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    // AJAX: current weather for one place as JSON
    public function show(Place $place, WeatherService $weather)
    {
        try {
            return response()->json($weather->forPlace($place));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Weather is not available right now.'], 503);
        }
    }
}
