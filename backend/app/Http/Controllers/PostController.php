<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    // Injeção de dependência (o Laravel entende e injeta o Service aqui)
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        // A busca simples pode ficar aqui no controller
        $posts = Post::with(['author', 'likes', 'comments.user'])->latest()->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:20480',
            'caption' => 'nullable|string|max:255'
        ]);

        $post = $this->postService->createPost(
            $request->user()->id, 
            $request->all(), 
            $request->file('image')
        );

        return response()->json($post->load('author'), 201);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Validação de segurança (regra de negócio)
        if (request()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        $this->postService->deletePost($post);

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}