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
        Schema::table('patients', function (Blueprint $table) {


            $table->string('secondary_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_id')->nullable();
            $table->string('primary_physician')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('current_medications')->nullable();

            // Administrative
            $table->string('status')->default('active');
            $table->string('mrn')->unique()->nullable(); // Medical Record Number

            // Risk Assessment
            $table->string('risk_level')->nullable();
            $table->json('alerts')->nullable();
            $table->string('fall_risk')->nullable();
            $table->text('dietary_restrictions')->nullable();

            // Extended Medical History
            $table->text('family_history')->nullable();
            $table->text('surgical_history')->nullable();
            $table->string('smoking_status')->nullable();
            $table->string('alcohol_use')->nullable();
            $table->string('exercise_habits')->nullable();
            $table->json('immunizations')->nullable();
            $table->date('last_physical_date')->nullable();

            $table->string('philhealth_number')->nullable()->after('current_medications');
            $table->string('blood_type')->nullable()->after('philhealth_number');
            $table->decimal('height', 5, 2)->nullable()->after('blood_type'); // in cm
            $table->decimal('weight', 5, 2)->nullable()->after('height'); // in kg
            $table->decimal('bmi', 4, 2)->nullable()->after('weight');
            $table->string('occupation')->nullable()->after('bmi');
            $table->string('civil_status')->nullable()->after('occupation');
            $table->string('nationality')->nullable()->after('civil_status');
            $table->string('religion')->nullable()->after('nationality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'philhealth_number',
                'blood_type',
                'height',
                'weight',
                'occupation',
                'civil_status',
                'nationality',
                'religion',
                'secondary_phone',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'insurance_provider',
                'insurance_id',
                'primary_physician',
                'allergies',
                'chronic_conditions',
                'current_medications',

                // Administrative
                'status',
                'mrn', // Medical Record Number

                // Vital Signs (or create a separate vitals table)
                'height', // in cm
                'weight', // in kg
                'bmi',

                // Risk Assessment
                'risk_level',
                'alerts',
                'fall_risk',
                'dietary_restrictions',

                // Extended Medical History
                'family_history',
                'surgical_history',
                'smoking_status',
                'alcohol_use',
                'exercise_habits',
                'immunizations',
                'last_physical_date'
            ]);
        });
    }
};
