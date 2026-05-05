<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color_code', 'is_active'];

    public function reports()
    {
        return $this->hasMany(Report::class, 'category_id');
    }
}