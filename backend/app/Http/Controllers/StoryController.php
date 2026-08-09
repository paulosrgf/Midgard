<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StoryController extends Controller
{
    // Endpoint de listagem filtrando pela expiração de 24h
    public function index(Request $request)
    {
        $stories = Story::with('user')
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('user_id'); // Agrupa os stories por usuário para facilitar no Frontend

        return response()->json($stories);
    }

    // Endpoint de criação de story
    public function store(Request $request)
    {
        $request->validate([
            'media' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $path = $request->file('media')->store('stories', 'public');

        $story = Story::create([
            'user_id' => $request->user()->id,
            'media_path' => $path,
            'expires_at' => Carbon::now()->addHours(24), // Expira em exatamente 24 horas
        ]);

        return response()->json($story, 201);
    }
}