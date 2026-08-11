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

    // FUNÇÃO DE ATUALIZAÇÃO DO PERFIL
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Trava de segurança: um usuário só pode editar a si mesmo
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Acesso negado pelos deuses.'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'avatar' => 'nullable|image|max:5120' // 5MB max
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        // Lógica de upload do Avatar
        if ($request->hasFile('avatar')) {
            // Apaga a foto antiga se existir
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return response()->json($user);
    }
    // Adicione esta função no final do seu UserController
    public function posts($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // O "with" é crucial aqui! Ele traz os relacionamentos para o Vue não quebrar.
        $posts = $user->posts()
                      ->with(['author', 'likes', 'comments'])
                      ->latest()
                      ->get();

        return response()->json($posts);
    }
}