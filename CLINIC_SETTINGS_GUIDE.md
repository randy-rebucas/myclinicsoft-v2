# Clinic Settings Management Guide

## Overview
The clinic information on the welcome page is now fully configurable through the admin panel. All clinic details including name, address, contact information, and hours can be managed from the Nova admin interface.

## Available Settings

### Basic Information
- **clinic_name**: The name of the clinic (e.g., "Kidzklinika")
- **clinic_tagline**: Main tagline displayed on the homepage
- **clinic_description**: Description text used in various sections

### Address Information
- **clinic_address**: Street address (e.g., "Zone 11, Baybay City, Leyte")
- **clinic_city**: City name (e.g., "Baybay City")
- **clinic_state**: State/Province (e.g., "Leyte")
- **clinic_zip**: Postal/ZIP code (e.g., "6521")
- **clinic_country**: Country (e.g., "Philippines")

### Contact Information
- **clinic_phone**: Main phone number (e.g., "(555) 123-4567")
- **clinic_emergency_phone**: Emergency phone number (e.g., "(555) 999-8888")
- **clinic_email**: Contact email address (e.g., "info@kidzklinika.com")
- **clinic_website**: Website URL (e.g., "https://kidzklinika.com")

### Operating Hours
- **clinic_hours_weekdays**: Weekday hours (e.g., "Monday - Friday: 8:00 AM - 6:00 PM")
- **clinic_hours_saturday**: Saturday hours (e.g., "Saturday: 9:00 AM - 1:00 PM")
- **clinic_hours_sunday**: Sunday hours (e.g., "Sunday: Closed")

## How to Update Settings

### Method 1: Through Nova Admin Panel
1. Log in to the Nova admin panel
2. Navigate to "Settings" in the sidebar
3. Find the setting you want to update
4. Click on the setting to edit
5. Update the value and save

### Method 2: Through Database
You can also update settings directly in the database:
```sql
UPDATE settings SET value = 'New Value' WHERE key = 'clinic_name';
```

### Method 3: Through Artisan Command
To reset all settings to default values:
```bash
php artisan clinic:seed-settings
```

## Where Settings Are Used

### Welcome Page Sections
- **Hero Section**: Clinic name, tagline, and description
- **Navigation**: Clinic name in header
- **Contact Section**: Full address, phone numbers, email, and hours
- **Map Section**: Clinic name and address
- **Emergency Banner**: Emergency phone number
- **Footer**: Clinic name, description, and contact info
- **FAQ Section**: Phone numbers and hours

### Helper Functions
The `ClinicSettings` helper class provides easy access to all settings:

```php
// Get clinic name
$name = \App\Helpers\ClinicSettings::name();

// Get full address
$address = \App\Helpers\ClinicSettings::fullAddress();

// Get phone number
$phone = \App\Helpers\ClinicSettings::phone();

// Get emergency phone
$emergency = \App\Helpers\ClinicSettings::emergencyPhone();

// Get email
$email = \App\Helpers\ClinicSettings::email();

// Get hours (returns array with formatted version)
$hours = \App\Helpers\ClinicSettings::hours();
```

## Default Values
If settings are not configured, the system will use these default values:
- **Name**: "Kidzklinika"
- **Address**: "Zone 11, Baybay City, Leyte"
- **Phone**: "(555) 123-4567"
- **Emergency**: "(555) 999-8888"
- **Email**: "info@kidzklinika.com"
- **Hours**: Standard medical clinic hours

## Important Notes
- Settings are cached for performance, so changes may take a moment to appear
- All phone numbers are automatically formatted as clickable links
- Email addresses are automatically formatted as mailto links
- Address information is combined to create a full address display
- Hours are formatted with HTML line breaks for proper display

## Troubleshooting
If settings don't appear to be updating:
1. Clear the application cache: `php artisan cache:clear`
2. Clear the config cache: `php artisan config:clear`
3. Restart the web server
4. Check that the settings table exists and has the correct data
