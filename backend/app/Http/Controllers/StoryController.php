<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Services\StoryService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StoryController extends Controller
{
    protected $storyService;

    public function __construct(StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    #[OA\Get(
        path: "/stories",
        summary: "Listar Mundos (Stories)",
        description: "Retorna apenas os stories ativos (que ainda não atingiram o limite de 24 horas)",
        tags: ["Mundos"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Mundos recuperados com sucesso")]
    public function index()
    {
        $stories = Story::with('user')
                        ->where('expires_at', '>', now())
                        ->latest()
                        ->get();
                        
        return response()->json($stories);
    }

    #[OA\Post(
        path: "/stories",
        summary: "Forjar um Mundo (Criar Story)",
        description: "Faz o upload de uma imagem para o Supabase com regra de expiração de 24 horas",
        tags: ["Mundos"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                required: ["image"],
                properties: [
                    new OA\Property(property: "image", type: "string", format: "binary", description: "A imagem do story")
                ]
            )
        )
    )]
    #[OA\Response(response: 201, description: "Mundo criado com sucesso")]
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:20480'
        ]);

        $story = $this->storyService->createStory(
            $request->user()->id, 
            $request->file('image')
        );

        return response()->json($story->load('user'), 201);
    }

    #[OA\Delete(
        path: "/stories/{id}",
        summary: "Destruir Mundo (Excluir Story)",
        description: "Apaga o story da nuvem prematuramente (antes das 24h) pelo próprio autor",
        tags: ["Mundos"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID do Mundo", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Mundo destruído")]
    #[OA\Response(response: 403, description: "Acesso negado")]
    public function destroy($id)
    {
        $story = Story::findOrFail($id);

        if (request()->user()->id !== $story->user_id) {
            return response()->json(['message' => 'Acesso negado pelos deuses.'], 403);
        }

        $this->storyService->deleteStory($story);

        return response()->json(['message' => 'Mundo destruído com sucesso.']);
    }
}