<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Função que DEVOLVE os posts para o Feed (GET)
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments.user'])->latest()->get();
        $currentUserId = $request->user() ? $request->user()->id : null;

        $formattedPosts = $posts->map(function ($post) use ($currentUserId) {
            // Garante que se o usuário dono do post não existir, ele não quebra a aplicação
            $authorName = $post->user ? ($post->user->name ?? $post->user->username) : 'Usuário Desconhecido';
            $authorAvatar = $post->user->avatar ?? 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop';

            return [
                'id' => $post->id,
                'author' => [
                    'username' => $authorName,
                    'avatar' => $authorAvatar,
                ],
                'image' => asset('storage/' . $post->image_path),
                'likes' => $post->likes ? $post->likes->count() : 0,
                'isLiked' => $currentUserId && $post->likes ? $post->likes->contains('user_id', $currentUserId) : false,
                'caption' => $post->caption,
                'comments' => $post->comments ? $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'username' => $comment->user ? ($comment->user->name ?? 'Usuário') : 'Usuário',
                        'body' => $comment->body
                    ];
                }) : [],
            ];
        });

        return response()->json($formattedPosts);
    }

    // Função que SALVA um novo post (POST)
    public function store(Request $request)
    {
        // 1. Valida se realmente veio uma imagem
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200', // max 5MB
            'caption' => 'nullable|string'
        ]);

        // 2. Salva a imagem fisicamente na pasta 'storage/app/public/posts'
        $imagePath = $request->file('image')->store('posts', 'public');

        // 3. Cria o registro no banco de dados, amarrado ao usuário que enviou o token!
        $post = $request->user()->posts()->create([
            'image_path' => $imagePath,
            'caption' => $request->caption,
        ]);

        return response()->json(['message' => 'Post criado com sucesso!', 'post' => $post], 201);
    }
}