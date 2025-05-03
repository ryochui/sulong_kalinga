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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unique('email', 'unified_users_email_unique'); // Explicit unique constraint name
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile')->nullable();
            $table->unsignedTinyInteger('role_id');
            $table->string('status')->nullable();
            $table->string('user_type'); // 'cose' or 'portal'
            $table->unsignedBigInteger('cose_user_id')->nullable();
            $table->unsignedBigInteger('portal_account_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
