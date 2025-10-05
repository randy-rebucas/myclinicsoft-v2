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
        Schema::table('encounters', function (Blueprint $table) {
            // Add composite index for doctor_id and encounter_date for better performance
            $table->index(['doctor_id', 'encounter_date'], 'idx_encounters_doctor_date');
            
            // Add index for encounter_date for month/year queries
            $table->index('encounter_date', 'idx_encounters_date');
            
            // Add index for patient_id for patient-specific queries
            $table->index('patient_id', 'idx_encounters_patient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropIndex('idx_encounters_doctor_date');
            $table->dropIndex('idx_encounters_date');
            $table->dropIndex('idx_encounters_patient');
        });
    }
};