<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'report_status_histories';
    
    public $timestamps = false; // Không có updated_at, chỉ có created_at

    protected $fillable = [
        'report_id', 'changed_by_user_id', 'old_status', 'new_status', 'note', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}