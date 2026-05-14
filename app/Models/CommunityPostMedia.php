<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPostMedia extends Model
{
    protected $fillable = ['post_id', 'file_path', 'file_type', 'sort_order'];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}