<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'max_borrow_days',
        'fine_per_day',
        'items_per_page',
    ];

    protected $casts = [
        'max_borrow_days' => 'integer',
        'fine_per_day' => 'decimal:2',
        'items_per_page' => 'integer',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Helper method to get setting for a school
    public static function getForSchool($schoolId)
    {
        return self::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'max_borrow_days' => 14,
                'fine_per_day' => 1000,
                'items_per_page' => 10,
            ]
        );
    }
}
