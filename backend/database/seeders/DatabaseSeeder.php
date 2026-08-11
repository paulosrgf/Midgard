<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria o SEU usuário principal para você não precisar criar conta toda vez
        $masterUser = User::create([
            'name' => 'Paulo (DevOps)',
            'username' => 'paulo_admin',
            'email' => 'admin@midgard.com',
            'password' => Hash::make('senha123'),
        ]);

        // 2. Cria 5 usuários falsos usando a Factory padrão do Laravel
        $usuarios = User::factory(5)->create();

        // 3. Para cada usuário falso criado, vamos forjar 2 Sagas (Posts)
        foreach ($usuarios as $user) {
            Post::factory(2)->create([
                'user_id' => $user->id
            ]);
        }

        // 4. Cria 3 Sagas exclusivas para o seu usuário principal
        Post::factory(3)->create([
            'user_id' => $masterUser->id
        ]);
    }
}