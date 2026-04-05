<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'level',
        'major',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
