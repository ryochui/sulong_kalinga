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

//     /**
//      * Get the name of the unique identifier for the user.
//      *
//      * @return string
//      */
//     public function getAuthIdentifierName()
//     {
//         return 'email_address';
//     }

//     /**
//      * Get the unique identifier for the user.
//      *
//      * @return mixed
//      */
//     public function getAuthIdentifier()
//     {
//         return $this->email_address;
//     }

//     /**
//      * Get the password for the user.
//      *
//      * @return string
//      */
//     public function getAuthPassword()
//     {
//         return $this->password_hash;
//     }

//     /**
//      * Get the token value for the "remember me" session.
//      *
//      * @return string|null
//      */
//     public function getRememberToken()
//     {
//         return $this->remember_token;
//     }

//     /**
//      * Set the token value for the "remember me" session.
//      *
//      * @param string|null $value
//      * @return void
//      */
//     public function setRememberToken($value)
//     {
//         $this->remember_token = $value;
//     }

//     /**
//      * Get the column name for the "remember me" token.
//      *
//      * @return string
//      */
//     public function getRememberTokenName()
//     {
//         return 'remember_token';
//     }
}