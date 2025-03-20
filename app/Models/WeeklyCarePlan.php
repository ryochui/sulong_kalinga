<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyCarePlan extends Model
{
    use HasFactory;

    protected $table = 'weekly_care_plans'; // Table name
    protected $primaryKey = 'weekly_care_plan_id'; // Explicitly set the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'beneficiary_id', 'care_worker_id', 'care_manager_id', 'vital_signs_id', 'date', 'assessment', 'evaluation_recommendations', 'assessment_summary_draft', 'assessment_translation_draft', 'evaluation_summary_draft', 'evaluation_translation_draft', 'created_by', 'updated_by'
    ];
}