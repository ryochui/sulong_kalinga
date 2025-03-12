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
        Schema::table('general_care_plans', function (Blueprint $table) {
            $table->foreign('beneficiary_id')->references('beneficiary_id')->on('beneficiaries')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_care_plans', function (Blueprint $table) {
            //
        });
    }
};
