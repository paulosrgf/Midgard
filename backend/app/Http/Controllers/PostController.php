<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[OA\Get(
        path: "/posts",
        summary: "Listar Sagas (Feed)",
        description: "Retorna todas as postagens do salão com seus autores, curtidas e comentários",
        tags: ["Sagas"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Sagas recuperadas com sucesso")]
    public function index()
    {
        $posts = Post::with(['author', 'likes', 'comments.user'])->latest()->get();
        return response()->json($posts);
    }

    #[OA\Post(
        path: "/posts",
        summary: "Forjar uma Saga (Criar Post)",
        description: "Faz o upload de uma imagem para o Supabase e cria a postagem",
        tags: ["Sagas"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                required: ["image"],
                properties: [
                    new OA\Property(property: "image", type: "string", format: "binary", description: "A imagem da saga"),
                    new OA\Property(property: "caption", type: "string", description: "Legenda opcional", nullable: true)
                ]
            )
        )
    )]
    #[OA\Response(response: 201, description: "Saga forjada com sucesso")]
    public function store(Request $request)
    {
        // Log para depurar no Render se o arquivo chegou no PHP
        \Illuminate\Support\Facades\Log::info('Request data:', $request->all());
        \Illuminate\Support\Facades\Log::info('Has file image?: ' . ($request->hasFile('image') ? 'SIM' : 'NAO'));

        if (!$request->hasFile('image')) {
            return response()->json([
                'message' => 'O arquivo de imagem não foi detectado pelo servidor.',
                'files_received' => $_FILES,
                'all_input' => $request->all()
            ], 422);
        }

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

    #[OA\Delete(
        path: "/posts/{id}",
        summary: "Destruir Saga (Excluir Post)",
        description: "Apaga a imagem da nuvem e deleta o registro (Apenas o autor pode fazer isso)",
        tags: ["Sagas"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID da Saga", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Saga destruída")]
    #[OA\Response(response: 403, description: "Acesso negado")]
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (request()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        $this->postService->deletePost($post);

        return response()->json(['message' => 'Postagem excluída com sucesso.']);
    }
}