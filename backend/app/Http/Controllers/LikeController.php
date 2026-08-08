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
            return response()->json([
                'liked' => false, 
                'likes_count' => $post->likes()->count()
            ]);
        }

        $post->likes()->create(['user_id' => $user->id]);
        
        return response()->json([
            'liked' => true, 
            'likes_count' => $post->likes()->count()
        ]);
    }
}