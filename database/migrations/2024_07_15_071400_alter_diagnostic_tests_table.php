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
        Schema::table('diagnostic_tests', function (Blueprint $table) {
            $table->dropForeign('diagnostic_tests_encounter_id_foreign');
            $table->dropColumn('encounter_id');

            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_tests', function (Blueprint $table) {
            $table->foreignId('encounter_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->dropForeign('diagnostic_tests_patient_id_foreign');
            $table->dropColumn('patient_id');
        });
    }
};
