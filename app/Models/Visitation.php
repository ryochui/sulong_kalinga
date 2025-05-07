<?php
// app/Models/Visitation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitation extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'visitation_id';
    
    protected $fillable = [
        'care_worker_id',
        'beneficiary_id',
        'visit_type',
        'visitation_date',
        'is_flexible_time',
        'start_time',
        'end_time',
        'notes',
        'date_assigned',
        'assigned_by',
        'status',
        'confirmed_by_beneficiary',
        'confirmed_by_family',
        'confirmed_on',
        'work_shift_id',
        'visit_log_id'
    ];
    
    protected $casts = [
        'visitation_date' => 'date',
        'date_assigned' => 'date',
        'is_flexible_time' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'confirmed_on' => 'datetime'
    ];
    
    /**
     * Get the care worker assigned to this visitation
     */
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id', 'id');
    }
    
    /**
     * Get the beneficiary for this visitation
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
    
    /**
     * Get the user who assigned this visitation
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }
    
    /**
     * Get the beneficiary who confirmed this visitation
     */
    public function confirmedByBeneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'confirmed_by_beneficiary', 'beneficiary_id');
    }
    
    /**
     * Get the family member who confirmed this visitation
     */
    public function confirmedByFamily()
    {
        return $this->belongsTo(FamilyMember::class, 'confirmed_by_family', 'family_member_id');
    }
    
    /**
     * Get the work shift associated with this visitation
     */
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id', 'work_shift_id');
    }
    
    /**
     * Get the visit log associated with this visitation
     */
    public function visitLog()
    {
        return $this->belongsTo(VisitationLog::class, 'visit_log_id', 'id');
    }
    
    /**
     * Get the recurring pattern for this visitation if any
     */
    public function recurringPattern()
    {
        return $this->hasOne(RecurringPattern::class, 'visitation_id', 'visitation_id');
    }
}