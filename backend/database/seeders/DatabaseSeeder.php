<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Follow;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Criar Usuários
        $users = [
            User::create(['name' => 'Paulo Sergio', 'username' => 'paulosrgf', 'email' => 'paulo@midgard.com', 'password' => Hash::make('password'), 'bio' => 'FullStack Dev | DevOps & Cloud']),
            User::create(['name' => 'Cyber Sec', 'username' => 'cyber_sec_student', 'email' => 'cyber@midgard.com', 'password' => Hash::make('password'), 'bio' => 'Estudante de Cyber Security']),
            User::create(['name' => 'DevOps Cloud', 'username' => 'devops_cloud', 'email' => 'devops@midgard.com', 'password' => Hash::make('password'), 'bio' => 'Amante de infraestrutura.']),
            User::create(['name' => 'Meteor Rider', 'username' => 'meteor_rider', 'email' => 'meteor@midgard.com', 'password' => Hash::make('password'), 'bio' => 'Royal Enfield Meteor 350.']),
        ];

        // 2. Sistema de Follows (Todo mundo segue o Paulo, e o Paulo segue alguns)
        Follow::create(['follower_id' => $users[1]->id, 'followed_id' => $users[0]->id]);
        Follow::create(['follower_id' => $users[2]->id, 'followed_id' => $users[0]->id]);
        Follow::create(['follower_id' => $users[3]->id, 'followed_id' => $users[0]->id]);
        Follow::create(['follower_id' => $users[0]->id, 'followed_id' => $users[1]->id]);
        
        // *Nota: Evitei criar posts no Seeder porque os arquivos físicos das imagens
        // não existem no seu HD local (storage). Se eu criasse no banco, as imagens 
        // apareceriam quebradas no Frontend. Crie 2 ou 3 posts manualmente pela 
        // interface clicando em "Criar" para a demonstração ficar com imagens reais!
    }
}