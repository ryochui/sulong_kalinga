<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareNeeds extends Model
{
    use HasFactory;

    protected $table = 'care_needs'; // Table name
    protected $primaryKey = 'care_need_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'care_category_id', 'frequency', 'assistance_required'
    ];

    
}