<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSigns extends Model
{
    use HasFactory;

    protected $table = 'vital_signs'; // Table name
    protected $primaryKey = 'vital_signs_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'blood_pressure', 'body_temperature', 'pulse_rate', 'respiratory_rate'
    ];
}