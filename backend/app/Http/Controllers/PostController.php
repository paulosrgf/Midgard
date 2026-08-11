<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Função que DEVOLVE os posts para o Feed (GET)
    public function index(Request $request)
    {
        // 1. Trocado 'user' por 'author' para alinhar com o banco e o Vue
        $posts = Post::with(['author', 'likes', 'comments.user'])->latest()->get();
        $currentUserId = $request->user() ? $request->user()->id : null;

        $formattedPosts = $posts->map(function ($post) use ($currentUserId) {
            // 2. Trocado $post->user por $post->author
            $authorName = $post->author ? ($post->author->name ?? $post->author->username) : 'Guerreiro Caído';
            
            // Passamos null se não tiver foto para o Vue gerar o Avatar Inteligente
            $authorAvatar = $post->author->avatar ?? null; 

            return [
                'id' => $post->id,
                'author' => [
                    'id' => $post->author ? $post->author->id : null, // ID é obrigatório para clicar e ir pro perfil
                    'username' => $authorName,
                    'avatar' => $authorAvatar,
                ],
                'image_path' => $post->image_path, // Agora enviamos 'image_path' diretamente, que é o que o Vue procura
                'likes' => $post->likes ? $post->likes->count() : 0,
                'isLiked' => $currentUserId && $post->likes ? $post->likes->contains('user_id', $currentUserId) : false,
                'caption' => $post->caption,
                'comments' => $post->comments ? $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'username' => $comment->user ? ($comment->user->name ?? $comment->user->username) : 'Usuário',
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
        $request->validate([
            'image' => 'required|image|max:20480',
            'caption' => 'nullable|string|max:255'
        ]);

        $path = $request->file('image')->store('posts', 'public');

        $post = new \App\Models\Post();
        $post->user_id = $request->user()->id;
        $post->image_path = $path; 
        $post->caption = $request->caption;
        $post->save();

        return response()->json($post->load('author'), 201);
    }

    // Função que APAGA um post (DELETE)
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        if ($post->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}