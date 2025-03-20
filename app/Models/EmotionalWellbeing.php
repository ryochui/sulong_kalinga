<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmotionalWellbeing extends Model
{
    use HasFactory;

    protected $table = 'emotional_wellbeing'; // Table name
    protected $primaryKey = 'emotional_wellbeing_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'mood', 'social_interactions', 'emotional_support_needs'
    ];

    
}