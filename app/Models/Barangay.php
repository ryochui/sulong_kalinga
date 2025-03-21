<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $table = 'barangays'; // Table name
    protected $primaryKey = 'barangay_id'; // Explicitly set the primary key

     /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'barangay_name'
    ];
}
