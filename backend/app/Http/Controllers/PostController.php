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

        // Salva fisicamente no Supabase
        $path = $request->file('image')->store('posts', 'supabase');

        // A MÁGICA AQUI: Transforma o link da API (s3) no link Público (object/public)
        $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
        $fullUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/' . $path;

        $post = new \App\Models\Post();
        $post->user_id = $request->user()->id;
        $post->image_path = $fullUrl; // Salva o link público para o Frontend ler sem bloqueios
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
            // Recria a URL base pública para poder limpar e ficar só com o 'posts/foto.png'
            $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
            $bucketUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/';
            
            $relativePath = str_replace($bucketUrl, '', $post->image_path);
            
            \Illuminate\Support\Facades\Storage::disk('supabase')->delete($relativePath);
        }

        $post->delete();

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}