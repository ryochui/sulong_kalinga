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

    protected $table = 'cose_user'; // Updated table name

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'birthday', 'civil_status', 'educational_background',
        'mobile', 'landline', 'email_address', 'password_hash', 'current_address',
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
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    protected $casts = [
        'birthday' => 'date',
        'status_start_date' => 'date',
        'status_end_date' => 'date',
    ];
}
