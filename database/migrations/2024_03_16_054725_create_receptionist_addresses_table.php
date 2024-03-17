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
        Schema::create('receptionist_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receptionist_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptionist_addresses');
    }
};
