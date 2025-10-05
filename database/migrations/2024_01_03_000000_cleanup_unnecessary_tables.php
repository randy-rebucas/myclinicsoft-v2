<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop unnecessary tables that were created by the complex migration
        // These tables are not needed for core clinic operations
        
        // Marketing/Advertising tables
        Schema::dropIfExists('ads');
        Schema::dropIfExists('medication_items');
        
        // Subscription model tables
        Schema::dropIfExists('doctor_subscriptions');
        Schema::dropIfExists('subscription_plans');
        
        // Pharma rep features
        Schema::dropIfExists('med_rep_doctors');
        Schema::dropIfExists('med_representatives');
        
        // Optional medical features
        Schema::dropIfExists('family_histories');
        Schema::dropIfExists('immunizations');
        Schema::dropIfExists('diagnostic_tests');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('physical_examinations');
        Schema::dropIfExists('medical_conditions');
        
        // Business features
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('invoice_items');
        
        // Optional system features
        Schema::dropIfExists('practices');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('receptionists');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This cleanup migration doesn't have a meaningful rollback
        // as we're removing tables that are not essential
        // If you need to restore these tables, you would need to re-run
        // the original complex migration
    }
};
