<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'icon', 'is_outdoor'])]
class Category extends Model
{
    protected function casts(): array
    {
        return [
            'is_outdoor' => 'boolean',
        ];
    }

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }
}
