<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Services\StoryService;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    protected $storyService;

    // Injetando o Service (Padrão MSC)
    public function __construct(StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    public function index()
    {
        // Retorna APENAS os stories que ainda não expiraram (Regra das 24h)
        $stories = Story::with('user')
                        ->where('expires_at', '>', now())
                        ->latest()
                        ->get();
                        
        return response()->json($stories);
    }

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
}