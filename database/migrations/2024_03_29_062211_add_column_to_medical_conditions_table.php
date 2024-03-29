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
        Schema::table('medical_conditions', function (Blueprint $table) {
            $table->foreignId('encounter_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_conditions', function (Blueprint $table) {
            $table->dropForeign('medical_conditions_encounter_id_foreign');
            $table->dropColumn('encounter_id');
        });
    }
};
