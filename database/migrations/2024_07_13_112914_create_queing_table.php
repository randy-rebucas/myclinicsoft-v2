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
        Schema::create('queing', function (Blueprint $table) {
            $table->id();
            $table->string('que_number');
            $table->enum('status', ['waiting', 'in progress', 'completed', 'canceled'])->default('waiting');
            $table->json('metadata')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queing');
    }
};
