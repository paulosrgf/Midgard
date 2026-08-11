<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostService {
    
    public function createPost(int $userId, array $data, $imageFile) {
        $path = $imageFile->store('posts', 'supabase');

        $publicEndpoint = str_replace('/s3', '/object/public', env('SUPABASE_STORAGE_ENDPOINT'));
        $fullUrl = $publicEndpoint . '/' . env('SUPABASE_STORAGE_BUCKET') . '/' . $path;

        return Post::create([
            'user_id' => $userId,
            'image_path' => $fullUrl,
            'caption' => $data['caption'] ?? null
        ]);
    }

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