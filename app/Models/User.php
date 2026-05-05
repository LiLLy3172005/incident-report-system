<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'password', 'role', 'strikes', 'is_banned'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relationships
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ReportStatusHistory::class, 'changed_by_user_id');
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function canReport()
    {
        return !$this->is_banned && $this->strikes < 3;
    }

    public function addStrike()
    {
        $this->increment('strikes');
        if ($this->strikes >= 3) {
            $this->is_banned = true;
            $this->save();
        }
    }
}