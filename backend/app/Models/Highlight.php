<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    protected $fillable = ['user_id', 'title', 'cover'];

    public function stories()
    {
        return $this->belongsToMany(Story::class, 'highlight_story');
    }
}