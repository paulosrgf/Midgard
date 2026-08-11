<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostService {
    
    /**
     * Cria um post, envia para o Supabase e retorna o objeto
     */
    public function createPost(int $userId, array $data, $imageFile) {
        // 1. Upload para o Supabase
        $path = $imageFile->store('posts', 'supabase');

        // 2. Converte para URL pública
        $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
        $fullUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/' . $path;

        // 3. Salva no banco
        return Post::create([
            'user_id' => $userId,
            'image_path' => $fullUrl,
            'caption' => $data['caption'] ?? null
        ]);
    }

    /**
     * Remove o arquivo do Supabase e deleta o registro do banco
     */
    public function deletePost(Post $post) {
        if ($post->image_path) {
            $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
            $bucketUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/';
            $relativePath = str_replace($bucketUrl, '', $post->image_path);
            
            Storage::disk('supabase')->delete($relativePath);
        }
        return $post->delete();
    }
}