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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->constrained()->cascadeOnDelete();
            
            // Clinical Information
            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('physical_examination')->nullable();
            $table->text('investigation')->nullable();
            
            // Prescription Details
            $table->json('medications')->nullable(); // Store structured medication data
            $table->text('treatment_plan')->nullable();
            $table->text('instructions')->nullable();
            $table->text('notes')->nullable();
            
            // Follow-up
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_notes')->nullable();
            
            // Status and Tracking
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('active');
            $table->date('prescription_date');
            
            // Audit
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
