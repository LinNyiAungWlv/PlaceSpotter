<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // AJAX: like the place, or unlike it if already liked
    public function toggle(Request $request, Place $place)
    {
        $like = $place->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
        } else {
            $place->likes()->create(['user_id' => $request->user()->id]);
        }

        return response()->json([
            'liked' => ! $like,
            'count' => $place->likes()->count(),
        ]);
    }
}
