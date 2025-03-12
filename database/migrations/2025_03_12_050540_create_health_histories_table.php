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
        Schema::create('health_histories', function (Blueprint $table) {
            $table->increments('health_history_id');
            $table->integer('general_care_plan_id')->after('health_history_id');
            $table->integer('history_category_id')->after('general_care_plan_id');
            $table->text('history_description')->after('history_category_id');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('general_care_plan_id')->references('general_care_plan_id')->on('general_care_plans')->onDelete('cascade');
            $table->foreign('history_category_id')->references('history_category_id')->on('history_categories')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_histories');
    }
};
