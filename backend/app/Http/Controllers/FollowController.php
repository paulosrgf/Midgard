<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, $username)
    {
        $me = $request->user();
        $targetUser = User::where('username', $username)->firstOrFail();

        // Regra do plano: usuário não pode seguir a si mesmo[cite: 1]
        if ($me->id === $targetUser->id) {
            return response()->json(['message' => 'Você não pode seguir a si mesmo.'], 400);
        }

        $isFollowing = $me->following()->where('followed_id', $targetUser->id)->exists();

        if ($isFollowing) {
            $me->following()->detach($targetUser->id);
            return response()->json([
                'following' => false,
                'followers_count' => $targetUser->followers()->count()
            ]);
        }

        $me->following()->attach($targetUser->id);
        return response()->json([
            'following' => true,
            'followers_count' => $targetUser->followers()->count()
        ]);
    }
}