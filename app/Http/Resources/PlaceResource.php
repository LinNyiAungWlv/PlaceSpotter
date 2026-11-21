<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

// Decides which fields of a place are sent as JSON to the JavaScript on the page.
// Only public information goes out (no emails, ids of users or timestamps).
class PlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'excerpt' => Str::limit($this->description, 100),
            'category' => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'icon' => $this->category->icon,
            ],
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'photo_url' => $this->photo_url,
            'likes_count' => $this->likes_count ?? 0,
            'author' => $this->user->name,
            'url' => route('places.show', $this->resource),
            // only there when the query worked it out (Place::nearby), left out of /search
            'distance_km' => $this->when(isset($this->distance), fn () => round($this->distance, 2)),
        ];
    }
}
