<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $users = User::where('username', 'like', "%{$query}%")
                     ->orWhere('name', 'like', "%{$query}%")
                     ->limit(15) // Paginação/Limite pra não explodir o banco
                     ->get()
                     ->map(function ($user) {
                         return [
                             'id' => $user->id,
                             'name' => $user->name,
                             'username' => $user->username,
                             'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop',
                         ];
                     });

        return response()->json($users);
    }
}