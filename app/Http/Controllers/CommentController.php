<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'comment' => 'required|string',
        'movie_id' => 'required|exists:movies,id',
    ]);

    $comment = Comment::create([
        'comment' => $request->comment,
        'movie_id' => $request->movie_id,
        'user_id' => auth()->id(),
    ]);

    return response()->json(['comment' => $comment->load('user')], 201);
}

}
