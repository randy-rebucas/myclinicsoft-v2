<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Clinic;
use Illuminate\Support\Collection;

class ClinicSettingsService
{
    /**
     * Get a setting value for a clinic.
     *
     * @param Clinic $clinic
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(Clinic $clinic, string $key, $default = null)
    {
        $setting = Setting::where('clinic_id', $clinic->id)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value for a clinic.
     *
     * @param Clinic $clinic
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public function setSetting(Clinic $clinic, string $key, $value): Setting
    {
        return Setting::updateOrCreate(
            [
                'clinic_id' => $clinic->id,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * Get all settings for a clinic.
     *
     * @param Clinic $clinic
     * @return Collection
     */
    public function getAllSettings(Clinic $clinic): Collection
    {
        return Setting::where('clinic_id', $clinic->id)
            ->get()
            ->keyBy('key');
    }

    /**
     * Set multiple settings for a clinic.
     *
     * @param Clinic $clinic
     * @param array $settings
     * @return Collection
     */
    public function setMultipleSettings(Clinic $clinic, array $settings): Collection
    {
        $createdSettings = collect();

        foreach ($settings as $key => $value) {
            $createdSettings->push($this->setSetting($clinic, $key, $value));
        }

        return $createdSettings;
    }

    /**
     * Delete a setting for a clinic.
     *
     * @param Clinic $clinic
     * @param string $key
     * @return bool
     */
    public function deleteSetting(Clinic $clinic, string $key): bool
    {
        return Setting::where('clinic_id', $clinic->id)
            ->where('key', $key)
            ->delete() > 0;
    }

    /**
     * Get clinic operating hours.
     *
     * @param Clinic $clinic
     * @return array
     */
    public function getOperatingHours(Clinic $clinic): array
    {
        $hours = $this->getSetting($clinic, 'operating_hours', []);
        return is_string($hours) ? json_decode($hours, true) : $hours;
    }

    /**
     * Set clinic operating hours.
     *
     * @param Clinic $clinic
     * @param array $hours
     * @return Setting
     */
    public function setOperatingHours(Clinic $clinic, array $hours): Setting
    {
        return $this->setSetting($clinic, 'operating_hours', json_encode($hours));
    }

    /**
     * Get clinic consultation fee.
     *
     * @param Clinic $clinic
     * @return float
     */
    public function getConsultationFee(Clinic $clinic): float
    {
        return (float) $this->getSetting($clinic, 'consultation_fee', 0);
    }

    /**
     * Set clinic consultation fee.
     *
     * @param Clinic $clinic
     * @param float $fee
     * @return Setting
     */
    public function setConsultationFee(Clinic $clinic, float $fee): Setting
    {
        return $this->setSetting($clinic, 'consultation_fee', $fee);
    }

    /**
     * Get clinic appointment duration.
     *
     * @param Clinic $clinic
     * @return int
     */
    public function getAppointmentDuration(Clinic $clinic): int
    {
        return (int) $this->getSetting($clinic, 'appointment_duration', 30);
    }

    /**
     * Set clinic appointment duration.
     *
     * @param Clinic $clinic
     * @param int $duration
     * @return Setting
     */
    public function setAppointmentDuration(Clinic $clinic, int $duration): Setting
    {
        return $this->setSetting($clinic, 'appointment_duration', $duration);
    }

    /**
     * Get clinic notification settings.
     *
     * @param Clinic $clinic
     * @return array
     */
    public function getNotificationSettings(Clinic $clinic): array
    {
        $settings = $this->getSetting($clinic, 'notification_settings', []);
        return is_string($settings) ? json_decode($settings, true) : $settings;
    }

    /**
     * Set clinic notification settings.
     *
     * @param Clinic $clinic
     * @param array $settings
     * @return Setting
     */
    public function setNotificationSettings(Clinic $clinic, array $settings): Setting
    {
        return $this->setSetting($clinic, 'notification_settings', json_encode($settings));
    }

    /**
     * Reset all settings for a clinic to defaults.
     *
     * @param Clinic $clinic
     * @return bool
     */
    public function resetToDefaults(Clinic $clinic): bool
    {
        $defaultSettings = [
            'consultation_fee' => 0,
            'appointment_duration' => 30,
            'operating_hours' => json_encode([
                'monday' => ['start' => '09:00', 'end' => '17:00'],
                'tuesday' => ['start' => '09:00', 'end' => '17:00'],
                'wednesday' => ['start' => '09:00', 'end' => '17:00'],
                'thursday' => ['start' => '09:00', 'end' => '17:00'],
                'friday' => ['start' => '09:00', 'end' => '17:00'],
                'saturday' => ['start' => '09:00', 'end' => '13:00'],
                'sunday' => ['start' => '00:00', 'end' => '00:00'],
            ]),
            'notification_settings' => json_encode([
                'appointment_reminders' => true,
                'queue_updates' => true,
                'prescription_ready' => true,
            ]),
        ];

        $this->setMultipleSettings($clinic, $defaultSettings);
        return true;
    }
}
