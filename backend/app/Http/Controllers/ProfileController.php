<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $posts = Post::where('user_id', $user->id)
                    ->with(['likes', 'comments'])
                    ->latest()
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'image' => asset('storage/' . $post->image_path),
                            'likes_count' => $post->likes ? $post->likes->count() : 0,
                            'comments_count' => $post->comments ? $post->comments->count() : 0,
                        ];
                    });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username ?? 'Usuário',
                'bio' => $user->bio ?? 'Adicione uma bio...',
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop',
                'posts_count' => $posts->count(),
            ],
            'posts' => $posts
        ]);
    }

    public function showUser(Request $request, $username)
    {
        $targetUser = \App\Models\User::where('username', $username)->firstOrFail();
        $me = $request->user();

        $posts = Post::where('user_id', $targetUser->id)
                    ->with(['likes', 'comments'])
                    ->latest()
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'image' => asset('storage/' . $post->image_path),
                            'likes_count' => $post->likes ? $post->likes->count() : 0,
                            'comments_count' => $post->comments ? $post->comments->count() : 0,
                        ];
                    });

        return response()->json([
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'username' => $targetUser->username,
                'bio' => $targetUser->bio,
                'avatar' => $targetUser->avatar ? asset('storage/' . $targetUser->avatar) : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop',
                'posts_count' => $posts->count(),
                'followers_count' => $targetUser->followers()->count(),
                'following_count' => $targetUser->following()->count(),
                // Retorna TRUE se o usuário logado já segue esta pessoa
                'is_followed_by_me' => $me ? $me->following()->where('followed_id', $targetUser->id)->exists() : false,
            ],
            'posts' => $posts
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // Valida os dados, garantindo que o username seja único (exceto para o próprio usuário)
        $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Atualiza os textos
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('username')) $user->username = $request->username;
        if ($request->has('bio')) $user->bio = $request->bio;

        // Gerencia o Upload do Avatar
        if ($request->hasFile('avatar')) {
            // Se já tinha um avatar salvo no storage, deleta o antigo para não lotar o HD
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Salva a nova imagem na pasta public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ]
        ]);
    }
}