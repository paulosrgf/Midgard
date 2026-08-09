<?php

namespace App\Http\Controllers;

use App\Models\Highlight;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    // Criar destaque e associar stories a ele (via a tabela pivot)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'story_ids' => 'required|array',
            'story_ids.*' => 'exists:stories,id',
        ]);

        $highlight = Highlight::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            // Pega a mídia do primeiro story selecionado para usar como capa do destaque
            'cover' => \App\Models\Story::find($request->story_ids[0])->media_path,
        ]);

        // Associa os stories ao destaque na tabela pivot (highlight_story)
        $highlight->stories()->attach($request->story_ids);

        return response()->json($highlight->load('stories'), 201);
    }

    // Listar stories de um destaque (sem o filtro de 24h)
    public function show($id)
    {
        $highlight = Highlight::with('stories')->findOrFail($id);
        
        return response()->json($highlight);
    }
}