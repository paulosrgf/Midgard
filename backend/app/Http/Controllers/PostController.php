<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Função que DEVOLVE os posts para o Feed (GET)
    public function index()
    {
        // Busca os posts mais recentes, trazendo também os dados do autor (user)
        $posts = Post::with('user')->latest()->get();
        
        // Vamos formatar para o Vue entender perfeitamente
        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'author' => [
                    'username' => $post->user->username ?? $post->user->name,
                    // Avatar fixo por enquanto, depois você faz o upload de avatar
                    'avatar' => 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop',
                ],
                // Monta a URL completa da imagem para o frontend conseguir exibir
                'image' => asset('storage/' . $post->image_path),
                'likes' => $post->likes ?? 0,
                'caption' => $post->caption,
                'comments' => 0,
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