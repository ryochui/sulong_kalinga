<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medications extends Model
{
    use HasFactory;

    protected $table = 'medications'; // Table name
    protected $primaryKey = 'medications_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'medication', 'dosage', 'frequency', 'administration_instructions'
    ];

    
}