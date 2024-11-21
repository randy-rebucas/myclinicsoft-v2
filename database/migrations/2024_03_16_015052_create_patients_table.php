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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
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

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
