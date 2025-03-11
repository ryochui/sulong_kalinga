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
        Schema::rename('users', 'cose_users'); // Rename users table to cose_user

        Schema::table('cose_users', function (Blueprint $table) {
            // Add new columns
            $table->string('first_name', 100)->after('id');
            $table->string('last_name', 100)->after('first_name');
            $table->date('birthday')->after('last_name');
            $table->string('civil_status', 20)->nullable()->after('birthday');
            $table->string('educational_background', 100)->nullable()->after('civil_status');
            $table->string('mobile', 11)->unique()->after('educational_background');
            $table->string('landline', 8)->nullable()->after('mobile');
            $table->string('email_address', 100)->unique()->after('landline');
            $table->string('password_hash', 255)->after('email_address');
            $table->text('current_address')->nullable()->after('password_hash');
            $table->string('gender', 10)->nullable()->after('current_address');
            $table->string('religion', 50)->nullable()->after('gender');
            $table->string('nationality', 50)->nullable()->after('religion');
            $table->string('volunteer_status', 20)->after('nationality');
            $table->date('status_start_date')->after('volunteer_status');
            $table->date('status_end_date')->after('status_start_date');
            $table->unsignedBigInteger('role_id')->after('status_end_date');
            $table->string('status', 20)->after('role_id');
            $table->unsignedBigInteger('organization_role_id')->nullable()->after('status');
            $table->unsignedBigInteger('assigned_municipality_id')->nullable()->after('organization_role_id');
            $table->binary('photo')->nullable()->after('assigned_municipality_id');
            $table->binary('government_issued_id')->nullable()->after('photo');
            $table->string('sss_id_number', 20)->nullable()->after('government_issued_id');
            $table->string('philhealth_id_number', 20)->nullable()->after('sss_id_number');
            $table->string('pagibig_id_number', 20)->nullable()->after('philhealth_id_number');
            $table->binary('cv_resume')->nullable()->after('pagibig_id_number');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at'); // Track who updated the record

            // Foreign Key Constraints
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            // Keep timestamps (created_at, updated_at)
            //$table->timestamps(); // Automatically adds created_at and updated_at

            // Keep remember_token if needed for authentication
            //$table->rememberToken(); // Already exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cose_users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'first_name', 'last_name', 'birthday', 'civil_status', 'educational_background', 
                'mobile', 'landline', 'email_address', 'password_hash', 'current_address', 
                'gender', 'religion', 'nationality', 'volunteer_status', 'status_start_date', 
                'status_end_date', 'role_id', 'status', 'organization_role_id', 'assigned_municipality_id', 
                'photo', 'government_issued_id', 'sss_id_number', 'philhealth_id_number', 
                'pagibig_id_number', 'cv_resume', 'updated_by'
            ]);
        });

        Schema::rename('cose_users', 'users'); // Revert table name change
    }
};
