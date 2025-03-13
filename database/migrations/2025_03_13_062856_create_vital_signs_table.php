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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->increments('vital_signs_id');
            $table->string('blood_pressure', 20)->after('vital_signs_id');
            $table->decimal('body_temperature', 4, 2)->after('blood_pressure');
            $table->integer('pulse_rate')->after('body_temperature');
            $table->integer('respiratory_rate')->after('pulse_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
