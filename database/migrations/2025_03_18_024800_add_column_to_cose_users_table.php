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
        Schema::table('cose_users', function (Blueprint $table) {
            $table->integer('barangay_id')->after('street_address');
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cose_users', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });
    }
};
