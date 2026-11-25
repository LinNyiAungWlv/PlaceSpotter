<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'color', 'is_outdoor'];

    protected $casts = ['is_outdoor' => 'boolean'];

    public function places()
    {
        return $this->hasMany(Place::class);
    }
}
