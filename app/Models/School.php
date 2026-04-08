<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'npsn',
        'address',
        'phone',
        'email',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
