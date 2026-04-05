<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
