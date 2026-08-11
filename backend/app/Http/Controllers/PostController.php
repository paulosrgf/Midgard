<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Função que DEVOLVE os posts para o Feed (GET)
    public function index(Request $request)
    {
        $posts = Post::with(['author', 'likes', 'comments.user'])->latest()->get();
        $currentUserId = $request->user() ? $request->user()->id : null;

        $formattedPosts = $posts->map(function ($post) use ($currentUserId) {
            $authorName = $post->author ? ($post->author->name ?? $post->author->username) : 'Guerreiro Caído';
            $authorAvatar = $post->author->avatar ?? null; 

            return [
                'id' => $post->id,
                'author' => [
                    'id' => $post->author ? $post->author->id : null,
                    'username' => $authorName,
                    'avatar' => $authorAvatar,
                ],
                'image_path' => $post->image_path,
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

        // 1. Salva o arquivo no Supabase (pasta 'posts')
        $path = $request->file('image')->store('posts', 'supabase');

        // 2. Monta a URL completa pública para salvar no banco
        $bucketUrl = env('SUPABASE_STORAGE_ENDPOINT') . '/' . env('SUPABASE_STORAGE_BUCKET');
        $fullUrl = $bucketUrl . '/' . $path;

        $post = new \App\Models\Post();
        $post->user_id = $request->user()->id;
        $post->image_path = $fullUrl; // Salva a URL pronta para o Vue ler
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

        // Deleta a imagem do Supabase
        if ($post->image_path) {
            $bucketUrl = env('SUPABASE_STORAGE_ENDPOINT') . '/' . env('SUPABASE_STORAGE_BUCKET') . '/';
            // Pega apenas o final do caminho (ex: posts/foto.png) para o comando delete funcionar
            $relativePath = str_replace($bucketUrl, '', $post->image_path);
            
            Storage::disk('supabase')->delete($relativePath);
        }

        $post->delete();

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}