<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'body' => 'required|string|max:255'
        ]);

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
            'username' => $user->name ?? 'Usuário',
        ], 201);
    }
}