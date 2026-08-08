<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Garante que a requisição não prossiga se o usuário for nulo por falha de token
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $posts = Post::where('user_id', $user->id)
                    ->with(['likes', 'comments'])
                    ->latest()
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'image' => asset('storage/' . $post->image_path),
                            // Os operadores ternários blindam a contagem contra nulos
                            'likes_count' => $post->likes ? $post->likes->count() : 0,
                            'comments_count' => $post->comments ? $post->comments->count() : 0,
                        ];
                    });

        return response()->json([
            'user' => [
                'username' => $user->name ?? $user->username ?? 'Usuário',
                'avatar' => $user->avatar ?? 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop',
                'posts_count' => $posts->count(),
            ],
            'posts' => $posts
        ]);
    }
}