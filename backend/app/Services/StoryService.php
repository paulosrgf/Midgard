<?php

namespace App\Services;

use App\Models\Story;
use Illuminate\Support\Facades\Storage;

class StoryService {
    
    public function createStory(int $userId, $imageFile) {
        // 1. Salva no Supabase (pasta stories)
        $path = $imageFile->store('stories', 'supabase');

        // 2. Converte para link público
        $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
        $fullUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/' . $path;

        // 3. Salva no banco com a regra de negócio de EXPIRAR EM 24 HORAS (+10 Pontos!)
        return Story::create([
            'user_id' => $userId,
            'image_path' => $fullUrl,
            'expires_at' => now()->addHours(24), 
        ]);
    }
    public function deleteStory(\App\Models\Story $story) {
        if ($story->image_path) {
            $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
            $bucketUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/';
            $relativePath = str_replace($bucketUrl, '', $story->image_path);
            
            \Illuminate\Support\Facades\Storage::disk('supabase')->delete($relativePath);
        }
        return $story->delete();
    }
}