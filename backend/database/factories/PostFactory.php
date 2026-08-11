<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        // Uma lista de imagens nórdicas do Unsplash para o seu feed nascer bonito
        $imagens = [
            'https://images.unsplash.com/photo-1531554868032-446702e5b72e?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1614728448981-d249f05d53ea?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1520114881075-8025287f3b60?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800&auto=format&fit=crop'
        ];

        return [
            // Pega um usuário aleatório ou cria um novo se não existir
            'user_id' => User::factory(), 
            // Pega uma imagem aleatória da nossa lista
            'image_path' => fake()->randomElement($imagens),
            // Gera uma frase aleatória para a legenda
            'caption' => fake()->sentence(6), 
        ];
    }
}