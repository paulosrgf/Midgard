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
        // 1. Valida se a imagem realmente chegou
        $request->validate([
            'image' => 'required|image|max:20480', // Máximo 20MB
            'caption' => 'nullable|string|max:255'
        ]);

        // 2. Salva o arquivo fisicamente na pasta storage/app/public/posts
        $path = $request->file('image')->store('posts', 'public');

        // 3. Salva no banco de dados com os nomes EXATOS das colunas
        $post = new \App\Models\Post();
        $post->user_id = $request->user()->id;
        $post->image_path = $path; // Aqui é onde o erro estava acontecendo!
        $post->caption = $request->caption;
        $post->save();

        // 4. Retorna o post recém-criado já empacotado com o Autor para o Vue não quebrar
        return response()->json($post->load('author'), 201);
    }
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Regra do plano: restrito ao próprio autor[cite: 1]
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        // Apaga a imagem do disco
        if ($post->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}