<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable
        = [
            'user_id',
            'name',
        ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }
}
