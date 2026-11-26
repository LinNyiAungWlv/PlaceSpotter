<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    // gets the current weather for a place from the Open-Meteo API
    public function forPlace(Place $place): array
    {
        // cache for 15 minutes so we don't call the API on every page view
        $current = Cache::remember("weather.place.{$place->id}", now()->addMinutes(15), function () use ($place) {
            return Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $place->latitude,
                'longitude' => $place->longitude,
                'current' => 'temperature_2m,weather_code,wind_speed_10m',
                'timezone' => 'Asia/Yangon',
            ])->throw()->json('current');
        });

        [$description, $icon] = $this->describe($current['weather_code']);
        [$good, $tip] = $this->visitTip($current, $place->category->is_outdoor);

        return [
            'temperature' => round($current['temperature_2m']),
            'wind' => round($current['wind_speed_10m']),
            'description' => $description,
            'icon' => $icon,
            'good_time' => $good,
            'tip' => $tip,
        ];
    }

    // turns the WMO weather code into text and an emoji
    private function describe(int $code): array
    {
        return match (true) {
            $code === 0 => ['Clear sky', '☀️'],
            $code <= 3 => ['Partly cloudy', '⛅'],
            $code <= 48 => ['Foggy', '🌫️'],
            $code <= 57 => ['Drizzle', '🌦️'],
            $code <= 67 => ['Rain', '🌧️'],
            $code <= 77 => ['Snow', '❄️'],
            $code <= 82 => ['Rain showers', '🌧️'],
            default => ['Thunderstorm', '⛈️'],
        };
    }

    // "Good time to visit?" based on the weather and whether the place is outdoors
    private function visitTip(array $current, bool $isOutdoor): array
    {
        $code = $current['weather_code'];
        $temp = $current['temperature_2m'];
        $isRaining = $code >= 51;

        if (! $isOutdoor) {
            return $isRaining
                ? [true, 'Perfect rainy-day spot, you will stay dry inside.']
                : [true, 'Good time to visit, this place is indoors.'];
        }

        if ($isRaining) {
            return [false, 'Not ideal right now, it is raining. Maybe try a café instead.'];
        }

        if ($temp >= 35) {
            return [false, 'Very hot right now. Visit in the early morning or evening.'];
        }

        return [true, 'Great time to visit!'];
    }
}
