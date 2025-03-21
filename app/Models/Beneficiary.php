<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;
    

    protected $table = 'beneficiaries';
    protected $primaryKey = 'beneficiary_id'; // Explicitly set the primary key
    protected $fillable = [
        'first_name', 'last_name', 'civil_status', 'gender', 'birthday', 'primary_caregiver',
        'mobile', 'landline', 'street_address', 'barangay_id', 'municipality_id', 'category_id',
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_mobile',
        'emergency_contact_email', 'beneficiary_status_id', 'status_reason', 'general_care_plan_id',
        'portal_account_id', 'beneficiary_signature', 'care_worker_signature', 'created_by', 'updated_by'
    ];

    /**
     * Get the category associated with the beneficiary.
     */
    public function category()
    {
        return $this->belongsTo(BeneficiaryCategory::class, 'category_id', 'category_id');
    }

    /**
     * Get the status associated with the beneficiary.
     */
    public function status()
    {
        return $this->belongsTo(BeneficiaryStatus::class, 'beneficiary_status_id', 'beneficiary_status_id');
    }

     /**
     * Get the municipality associated with the beneficiary.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    // Define the relationship to the Barangay model
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }    
}