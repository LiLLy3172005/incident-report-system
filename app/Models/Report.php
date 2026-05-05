<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'audio_url', 'latitude', 'longitude',
        'address_text', 'description', 'ai_label', 'ai_confidence', 'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ai_confidence' => 'float'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    // Methods
    public function isDeepfake()
    {
        return $this->ai_label === 'FAKE' && $this->ai_confidence > 0.85;
    }

    public function updateStatus($newStatus, $changedByUserId = null, $note = null)
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $newStatus]);
        
        ReportStatusHistory::create([
            'report_id' => $this->id,
            'changed_by_user_id' => $changedByUserId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}