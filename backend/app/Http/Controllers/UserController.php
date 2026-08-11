<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // FUNÇÃO DE BUSCA
    public function search(Request $request)
    {
        $q = $request->query('q');
        
        if (!$q) {
            return response()->json([]);
        }

        // Usa ILIKE do Postgres para ignorar maiúsculas/minúsculas
        $users = User::where('username', 'ILIKE', "%{$q}%")
                     ->orWhere('name', 'ILIKE', "%{$q}%")
                     ->limit(15)
                     ->get();

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Acesso negado pelos deuses.'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'avatar' => 'nullable|image|max:5120'
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        // Lógica de upload do Avatar na Nuvem (Supabase S3)
        if ($request->hasFile('avatar')) {
            
            // Apaga o avatar antigo do Supabase, se ele tiver um
            if ($user->avatar) {
                $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
                $bucketUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/';
                $relativePath = str_replace($bucketUrl, '', $user->avatar);
                \Illuminate\Support\Facades\Storage::disk('supabase')->delete($relativePath);
            }

            // Salva o arquivo novo na pasta 'avatars'
            $path = $request->file('avatar')->store('avatars', 'supabase');
            
            // Gera a URL pública para o frontend
            $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
            $user->avatar = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/' . $path;
        }

        $user->save();

        return response()->json($user);
    }
    // Adicione esta função no final do seu UserController
    public function posts($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Puxa os posts desse usuário já empacotados com o Autor, Likes e Comentários
        $posts = $user->posts()
                      ->with(['author', 'likes', 'comments'])
                      ->latest()
                      ->get();

        return response()->json($posts);
    }
    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return response()->json($user);
    }
}