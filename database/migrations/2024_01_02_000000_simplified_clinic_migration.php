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
        
        // Drop existing tables if they exist to avoid conflicts
        // Drop child tables first, then parent tables
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('vitals');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('encounters');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('clinic_doctors');
        Schema::dropIfExists('patient_doctors');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================
        // CORE LARAVEL TABLES (Required)
        // ========================================
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('email');
            $table->index('is_active');
            $table->index('last_login_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ========================================
        // PERMISSION SYSTEM (Spatie Laravel Permission)
        // ========================================
        
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        // ========================================
        // CORE CLINIC TABLES
        // ========================================

        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('license_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('logo')->nullable();
            $table->json('operating_hours')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for performance
            $table->index('name');
            $table->index('city');
            $table->index('state');
            $table->index('is_active');
            $table->index('email');
            $table->index('license_number');
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->string('specialty')->nullable();
            $table->string('license_number')->nullable();
            $table->string('npi_number')->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->text('bio')->nullable();
            $table->json('available_hours')->nullable();
            $table->boolean('is_active')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('meta')->nullable();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['first_name', 'last_name']);
            $table->index('phone_number');
            $table->index('specialty');
            $table->index('license_number');
            $table->index('npi_number');
            $table->index('is_active');
            $table->index('user_id');
            $table->index('clinic_id');
            $table->index('deleted_at');
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            
            // Extended patient information
            $table->string('secondary_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_id')->nullable();
            $table->string('primary_physician')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('current_medications')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('blood_type')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->string('occupation')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            
            // Administrative
            $table->string('status')->default('active');
            $table->string('mrn')->unique()->nullable();
            
            // Risk Assessment
            $table->string('risk_level')->nullable();
            $table->json('alerts')->nullable();
            $table->string('fall_risk')->nullable();
            $table->text('dietary_restrictions')->nullable();
            
            // Extended Medical History
            $table->text('family_history')->nullable();
            $table->text('surgical_history')->nullable();
            $table->string('smoking_status')->nullable();
            $table->string('alcohol_use')->nullable();
            $table->string('exercise_habits')->nullable();
            $table->json('immunizations')->nullable();
            $table->date('last_physical_date')->nullable();
            
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['first_name', 'last_name']);
            $table->index('phone_number');
            $table->index('date_of_birth');
            $table->index('status');
            $table->index('mrn');
            $table->index('user_id');
            $table->index('deleted_at');
        });

        // ========================================
        // MEDICAL RECORDS TABLES
        // ========================================

        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->string('chief_complaint')->nullable();
            $table->date('encounter_date');
            $table->time('encounter_time')->nullable();
            $table->enum('appointment_type', ['consultation', 'follow_up', 'emergency', 'routine_checkup'])->default('consultation');
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            
            // Indexes for performance
            $table->index('encounter_date');
            $table->index('encounter_time');
            $table->index('appointment_type');
            $table->index('status');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index(['patient_id', 'encounter_date']);
            $table->index(['doctor_id', 'encounter_date']);
            $table->index(['status', 'encounter_date']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('duration')->default(30)->comment('Duration in minutes');
            $table->enum('type', ['consultation', 'follow_up', 'emergency', 'routine_checkup'])->default('consultation');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('appointment_date');
            $table->index('appointment_time');
            $table->index('status');
            $table->index('type');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('clinic_id');
            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['clinic_id', 'appointment_date']);
            $table->index(['status', 'appointment_date']);
        });

        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('queue_number');
            $table->enum('status', ['waiting', 'called', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('waiting');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent', 'emergency'])->default('normal');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('queue_number');
            $table->index('status');
            $table->index('priority');
            $table->index('patient_id');
            $table->index('clinic_id');
            $table->index('doctor_id');
            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'queue_number']);
            $table->index(['doctor_id', 'status']);
            $table->index('called_at');
            $table->index('completed_at');
        });

        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('blood_pressure')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->integer('blood_sugar')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('patient_id');
            $table->index(['patient_id', 'created_at']);
        });

        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->string('allergen');
            $table->string('reaction');
            $table->string('severity');
            $table->text('notes')->nullable();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('allergen');
            $table->index('severity');
            $table->index('patient_id');
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('encounter_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('prescription_items');
            $table->text('notes')->nullable();
            
            // Indexes for performance
            $table->index('patient_id');
            $table->index('encounter_id');
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->integer('quantity')->nullable();
            $table->integer('refills')->default(0);
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('medication_name');
            $table->index('status');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['patient_id', 'status']);
        });

        // ========================================
        // RELATIONSHIP TABLES
        // ========================================

        Schema::create('clinic_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['clinic_id', 'doctor_id']);
        });

        Schema::create('patient_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unique(['patient_id', 'doctor_id']);
            $table->timestamps();
            
            // Indexes for performance
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('is_active');
            $table->index(['patient_id', 'is_active']);
            $table->index(['doctor_id', 'is_active']);
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->boolean('default')->default('0');
            $table->morphs('addressable'); // This automatically creates the addressable_type, addressable_id columns and index
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country', 2);
            $table->timestamps();
            
            // Indexes for performance (morphs already creates the addressable index)
            $table->index('city');
            $table->index('state');
            $table->index('postal_code');
            $table->index('country');
        });

        // ========================================
        // SYSTEM SUPPORT TABLES
        // ========================================

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject'); // This automatically creates the subject_type, subject_id columns and index
            $table->string('type');
            $table->text('description')->nullable();
            $table->json('changes')->nullable();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes for performance (morphs already creates the subject index)
            $table->index('type');
            $table->index('causer_id');
            $table->index('created_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->morphs('notifiable'); // This automatically creates the notifiable_type, notifiable_id columns and index
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance (morphs already creates the notifiable index)
            $table->index('type');
            $table->index('read_at');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event'); // created, updated, deleted, etc.
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('event');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('ip_address');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop system support tables first
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('settings');
        
        // Drop relationship tables
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('patient_doctors');
        Schema::dropIfExists('clinic_doctors');
        
        // Drop medical records tables
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('vitals');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('encounters');
        
        // Drop core clinic tables
        Schema::dropIfExists('patients');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('clinics');
        
        // Drop permission tables
        $tableNames = config('permission.table_names');
        if (!empty($tableNames)) {
            Schema::drop($tableNames['role_has_permissions']);
            Schema::drop($tableNames['model_has_roles']);
            Schema::drop($tableNames['model_has_permissions']);
            Schema::drop($tableNames['roles']);
            Schema::drop($tableNames['permissions']);
        }
        
        // Drop Laravel framework tables
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
