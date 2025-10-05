<?php

namespace App\Livewire\Forms;

use App\Models\Patient;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;
use App\Traits\GeneratesUserCredentials;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Enums\GenderEnum;


class PatientForm extends Form
{
    use GeneratesUserCredentials;

    public $newPatient;

    #[Validate('required')]
    public $first_name;

    #[Validate('required')]
    public $last_name;

    #[Validate('required|numeric|min:0|max:300')]
    public $height;

    #[Validate('required|numeric|min:0|max:500')]
    public $weight;

    #[Validate('required')]
    public $phone_number;

    #[Validate('required|in:male,female,unknown')]
    public $gender;


    public function setPatient(?Patient $patient = null)
    {
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->height = $patient->height;
        $this->weight = $patient->weight;
        $this->phone_number = $patient->phone_number;
        $this->gender = $patient->gender;
    }

    public function store(?Patient $patient)
    {
        $this->validate();

        if ($patient) {
            $this->update($patient);
        } else {
            $this->newPatient = $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'height', 'weight', 'gender');
    }

    public function update(Patient $patient)
    {
        $patient->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => $this->gender
        ]);
    }

    public function create()
    {
        // Check database connection first
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            throw new \Exception('Database connection is not available. Please try again later.');
        }

        $credentials = $this->generateCredentials($this->first_name, $this->last_name);

        try {
            $user = User::create([
                'name' => $credentials['username'],
                'email' => $credentials['email'],
                'password' => Hash::make('password'),
            ]);

            $patient = Patient::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'height' => $this->height,
                'weight' => $this->weight,
                'gender' => $this->gender,
                'user_id' => $user->id,
            ]);

            // Log patient creation activity (aligns with ActivitySeeder)
            $patient->recordActivity('created', 'Patient record was created');

            $currentUser = Auth::user();
            if ($currentUser->hasRole('doctor')) {
                $doctor = $currentUser->doctor;
                $patient->doctors()->attach($doctor->id, ['is_active' => true]);
            } else {
                $doctor = $currentUser->receptionist->doctor;
                $patient->doctors()->attach($doctor->id, ['is_active' => true]);
            }

            // Log patient information update activity
            $patient->recordActivity('updated', 'Patient information was updated');

            return $patient;
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            if (isset($user)) {
                $user->delete();
            }

            if (str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'No connection')) {
                throw new \Exception('Database connection failed. Please try again later.');
            }

            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception('A user with this email already exists. Please try again.');
            }

            throw new \Exception('Database error occurred while creating patient. Please try again.');
        } catch (\Exception $e) {
            // Handle other errors
            if (isset($user)) {
                $user->delete();
            }
            throw $e;
        }
    }

    public function clearInputs()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone_number = '';
        $this->height = '';
        $this->weight = '';
        $this->gender = '';
    }
}
