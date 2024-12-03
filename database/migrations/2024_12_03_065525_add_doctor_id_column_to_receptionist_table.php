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
        Schema::table('receptionists', function (Blueprint $table) {
            $table->foreignId('doctor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete(); // Add this line
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptionists', function (Blueprint $table) {
            $table->dropForeign('receptionists_doctor_id_foreign');
            $table->dropColumn('doctor_id');

        });
    }
};
