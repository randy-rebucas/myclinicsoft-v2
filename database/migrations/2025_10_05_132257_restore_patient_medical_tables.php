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
        // Create family_histories table
        Schema::create('family_histories', function (Blueprint $table) {
            $table->id();
            $table->string('relationship')->comment("Relation to the individual e.g., Parent, Sibling");
            $table->string('condition');
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('relationship');
            $table->index('condition');
            $table->index('patient_id');
        });

        // Create immunizations table
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->string('vaccine_name');
            $table->date('date_administered');
            $table->string('lot_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('vaccine_name');
            $table->index('date_administered');
            $table->index('patient_id');
            $table->index(['patient_id', 'created_at']);
        });

        // Create diagnostic_tests table
        Schema::create('diagnostic_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->date('test_date');
            $table->text('results');
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('test_name');
            $table->index('test_date');
            $table->index('patient_id');
            $table->index(['patient_id', 'test_date']);
        });

        // Create medical_conditions table
        Schema::create('medical_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('condition_name');
            $table->date('diagnosis_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('treatment_plan')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('condition_name');
            $table->index('diagnosis_date');
            $table->index('status');
            $table->index('patient_id');
            $table->index(['patient_id', 'status']);
        });

        // Create physical_examinations table
        Schema::create('physical_examinations', function (Blueprint $table) {
            $table->id();
            $table->string('examination_type');
            $table->text('findings');
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('examination_type');
            $table->index('patient_id');
            $table->index('encounter_id');
            $table->index(['patient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_examinations');
        Schema::dropIfExists('medical_conditions');
        Schema::dropIfExists('diagnostic_tests');
        Schema::dropIfExists('immunizations');
        Schema::dropIfExists('family_histories');
    }
};