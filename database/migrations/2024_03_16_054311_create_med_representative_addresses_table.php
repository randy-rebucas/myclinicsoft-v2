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
        Schema::create('med_representative_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_representative_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_representative_addresses');
    }
};
