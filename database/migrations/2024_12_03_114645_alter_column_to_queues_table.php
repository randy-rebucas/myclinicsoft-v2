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
        Schema::table('queues', function (Blueprint $table) {
            $table->dropForeign('queues_department_id_foreign');
            $table->dropColumn('department_id');

            $table->foreignId('clinic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->dropForeign('queues_clinic_id_foreign');
            $table->dropColumn('clinic_id');
        });
    }
};
