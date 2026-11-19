<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// user_id is not fillable on purpose: it is always set from the logged-in user
#[Fillable(['category_id', 'name', 'description', 'latitude', 'longitude', 'photo_path'])]
class Place extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // $place->photo_url gives the full URL of the photo, or null if there is none
    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo_path ? asset('storage/' . $this->photo_path) : null);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
