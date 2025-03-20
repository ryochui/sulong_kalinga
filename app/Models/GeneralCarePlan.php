<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralCarePlan extends Model
{
    use HasFactory;

    protected $table = 'general_care_plans'; // Table name
    protected $primaryKey = 'general_care_plan_id'; // Explicitly set the primary key
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'care_worker_id', 'emergency_plan', 'review_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'review_date' => 'date',
    ];
}