<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            // Instanciado manualmente para evitar erros caso 'user_id' não esteja no $fillable do model
            $like = new \App\Models\Like();
            $like->user_id = $user->id;
            $post->likes()->save($like);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked, 
            'likes_count' => $post->likes()->count()
        ]);
    }
}