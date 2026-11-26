<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    // AJAX: add a comment and send back its HTML
    public function store(Request $request, Place $place)
    {
        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ], [], ['body' => 'comment']);

        $comment = $place->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return response()->json([
            'html' => view('comments._comment', compact('comment'))->render(),
            'count' => $place->comments()->count(),
        ]);
    }

    // AJAX: delete your own comment
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $place = $comment->place;
        $comment->delete();

        return response()->json(['count' => $place->comments()->count()]);
    }
}
