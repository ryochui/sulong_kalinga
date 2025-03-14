<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weekly_care_plans', function (Blueprint $table) {
            $table->increments('weekly_care_plan_id');
            $table->integer('beneficiary_id')->after('weekly_care_plan_id');
            $table->integer('care_worker_id')->after('beneficiary_id');
            $table->integer('care_manager_id')->after('care_worker_id');
            $table->integer('vital_signs_id')->after('care_manager_id');
            $table->date('date');
            $table->text('assessment');
            $table->text('evaluation_recommendations');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

             // Foreign Key Constraints
             $table->foreign('beneficiary_id')->references('beneficiary_id')->on('beneficiaries')->onDelete('no action');
             $table->foreign('care_worker_id')->references('id')->on('cose_users')->onDelete('no action');
             $table->foreign('care_manager_id')->references('id')->on('cose_users')->onDelete('no action');
             $table->foreign('vital_signs_id')->references('vital_signs_id')->on('vital_signs')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_care_plans');
    }
};
