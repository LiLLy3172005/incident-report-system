<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class CommunityPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'content', 'status',
        'admin_note', 'reviewed_by', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function media()
    {
        return $this->hasMany(CommunityPostMedia::class, 'post_id')->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'post_id')->orderBy('created_at');
    }

    public function likes()
    {
        return $this->hasMany(CommunityLike::class, 'post_id');
    }

    // Scopes - ĐÃ THÊM TYPE HINT
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    // Helpers
    public function isLikedByUser(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function getFirstImageAttribute()
    {
        return $this->media->where('file_type', 'image')->first();
    }
}