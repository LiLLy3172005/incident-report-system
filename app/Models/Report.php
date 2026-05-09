<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

   protected $fillable = [
    'user_id', 'category_id', 'audio_url',
    'latitude', 'longitude', 'address_text',
    'description', 'ai_label', 'ai_confidence', 'status',
];

// Thêm cast
protected $casts = [
    'latitude' => 'float',
    'longitude' => 'float',
    'ai_confidence' => 'float',
    'is_anonymous' => 'boolean',
];
    /**
     * Get the user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the report.
     */
    public function category()
    {
        return $this->belongsTo(IncidentCategory::class);
    }

    /**
     * Get the status histories for the report.
     */
    public function statusHistories()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }
}