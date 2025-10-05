<?php

namespace App\Helpers;

class ClinicSettings
{
    /**
     * Get a clinic setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return config("settings.{$key}", $default);
    }

    /**
     * Get clinic name
     */
    public static function name()
    {
        return self::get('clinic_name', 'Kidzklinika');
    }

    /**
     * Get clinic address
     */
    public static function address()
    {
        return self::get('clinic_address', 'Zone 11, Baybay City, Leyte');
    }

    /**
     * Get full clinic address
     */
    public static function fullAddress()
    {
        $address = self::get('clinic_address', 'Zone 11, Baybay City, Leyte');
        $city = self::get('clinic_city', 'Baybay City');
        $state = self::get('clinic_state', 'Leyte');
        $zip = self::get('clinic_zip', '6521');
        $country = self::get('clinic_country', 'Philippines');
        
        return "{$address}, {$city}, {$state} {$zip}, {$country}";
    }

    /**
     * Get clinic phone
     */
    public static function phone()
    {
        return self::get('clinic_phone', '(555) 123-4567');
    }

    /**
     * Get clinic emergency phone
     */
    public static function emergencyPhone()
    {
        return self::get('clinic_emergency_phone', '(555) 999-8888');
    }

    /**
     * Get clinic email
     */
    public static function email()
    {
        return self::get('clinic_email', 'info@kidzklinika.com');
    }

    /**
     * Get clinic website
     */
    public static function website()
    {
        return self::get('clinic_website', 'https://kidzklinika.com');
    }

    /**
     * Get clinic hours
     */
    public static function hours()
    {
        $weekdays = self::get('clinic_hours_weekdays', 'Monday - Friday: 8:00 AM - 6:00 PM');
        $saturday = self::get('clinic_hours_saturday', 'Saturday: 9:00 AM - 1:00 PM');
        $sunday = self::get('clinic_hours_sunday', 'Sunday: Closed');
        
        return [
            'weekdays' => $weekdays,
            'saturday' => $saturday,
            'sunday' => $sunday,
            'formatted' => "{$weekdays}<br>{$saturday}<br>{$sunday}"
        ];
    }

    /**
     * Get clinic description
     */
    public static function description()
    {
        return self::get('clinic_description', 'Providing comprehensive pediatric care from newborns to adolescents in a warm, family-friendly environment with cutting-edge technology.');
    }

    /**
     * Get clinic tagline
     */
    public static function tagline()
    {
        return self::get('clinic_tagline', 'Caring for Your Children Like Our Own');
    }
}
