<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use App\Traits\RecordsActivity;
use App\Traits\Addressable;
use App\Traits\HasFullName;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use RecordsActivity;
    use Addressable;
    use HasFullName;
    public $timestamps = FALSE;

    protected $fillable = [
        'avatar',
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'gender',
        'user_id',
        'secondary_phone',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'insurance_provider',
        'insurance_id',
        'primary_physician',
        'allergies',
        'chronic_conditions',
        'current_medications',
        'philhealth_number',
        'blood_type',
        'height',
        'weight',
        'bmi',
        'occupation',
        'civil_status',
        'nationality',
        'religion',
        'status',
        'mrn',
        'risk_level',
        'alerts',
        'fall_risk',
        'dietary_restrictions',
        'family_history',
        'surgical_history',
        'smoking_status',
        'alcohol_use',
        'exercise_habits',
        'immunizations',
        'last_physical_date',
    ];

    protected $appends = [
        'full_name',
        'age'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'bmi' => 'decimal:2',
            'alerts' => 'array',
            'immunizations' => 'array',
            'last_physical_date' => 'date',
        ];
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'patients_index';
    }


    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->attributes['date_of_birth'])->age : null;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'patient_doctors')
            ->withTimestamps()
            ->withPivot('is_active');
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function allergies()
    {
        return $this->hasMany(Allergy::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function familyHistories()
    {
        return $this->hasMany(FamilyHistory::class);
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class);
    }

    public function diagnosticTests()
    {
        return $this->hasMany(DiagnosticTest::class);
    }

    public function medicalConditions()
    {
        return $this->hasMany(MedicalCondition::class);
    }

    public function physicalExaminations()
    {
        return $this->hasMany(PhysicalExamination::class);
    }

}
