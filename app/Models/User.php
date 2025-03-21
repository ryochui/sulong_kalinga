<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'cose_users'; // Updated table name

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'birthday', 'civil_status', 'educational_background',
        'mobile', 'landline', 'personal_email', 'email', 'password', 'address', 'barangay_id',
        'gender', 'religion', 'nationality', 'volunteer_status', 'status_start_date',
        'status_end_date', 'role_id', 'status', 'organization_role_id', 'assigned_municipality_id',
        'photo', 'government_issued_id', 'sss_id_number', 'philhealth_id_number',
        'pagibig_id_number', 'cv_resume', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'status_start_date' => 'date',
        'status_end_date' => 'date',
    ];

    /**
     * Get the municipality associated with the beneficiary.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'assigned_municipality_id', 'municipality_id');
    }

    // Define the relationship to the Barangay model
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }    
}