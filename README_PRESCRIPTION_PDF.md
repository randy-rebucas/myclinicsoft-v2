# Prescription PDF Generation System

## Overview
This system provides a comprehensive prescription PDF generation feature for the MyClinicSoft application. The prescription PDFs are perfectly sized for A4 paper and include all essential medical information in a professional, easy-to-read format.

## Features

### 1. Header Section
- **Clinic Information**: Displays clinic name, address, city, state, zip, phone, and email
- **Clinic Logo**: Automatically includes clinic logo if configured
- **Prescription Title**: Clear "PRESCRIPTION" header
- **Prescription Number**: Unique prescription ID and date

### 2. Patient Information Section
- **Patient Details**: Name, Patient ID, Date of Birth, Age, Gender
- **Complete Address**: Full address with proper formatting
- **Professional Layout**: Clean, organized grid layout with proper spacing

### 3. Prescription Items Section
- **Medication Table**: Organized table with medication details
- **Comprehensive Information**: 
  - Medication name and generic alternatives
  - Dosage information
  - Frequency and duration
  - Special instructions
  - Refill indicators (if applicable)
- **Professional Styling**: Alternating row colors for better readability

### 4. Footer Section
- **Practitioner Information**: Doctor's name and credentials
- **License Numbers**: PRC, PTR, and S2 license numbers
- **QR Code**: Patient ID QR code for easy verification
- **Professional Layout**: Balanced design with proper spacing

### 5. Additional Features
- **Date and Signature Fields**: Professional signature lines
- **Prescription Validity**: Clear validity period information
- **Pharmacy Instructions**: Standard pharmacy guidelines
- **Additional Notes**: Support for custom notes and follow-up information
- **Watermark**: Subtle "RX" watermark for authenticity

## Technical Implementation

### Dependencies
- **Laravel 11**: PHP framework
- **DomPDF**: PDF generation library
- **SimpleSoftwareIO QrCode**: QR code generation
- **Nova**: Admin panel integration

### Files Modified/Created

#### 1. PrintPrescriptionPDF Action
**Location**: `app/Nova/Actions/PrintPrescriptionPDF.php`
- Generates comprehensive prescription PDFs
- Handles patient and doctor data relationships
- Generates QR codes for patient identification
- Optimized PDF settings for A4 paper

#### 2. Prescription PDF Template
**Location**: `resources/views/pdfs/prescription.blade.php`
- Professional HTML template with CSS styling
- Responsive design optimized for PDF generation
- Comprehensive data handling with fallbacks
- Professional medical document styling

### Data Structure

#### Medication Model
```php
{
    "id": 1,
    "patient_id": 1,
    "prescription_items": [
        {
            "type": "medication-item",
            "fields": {
                "medication_name": "acetaminophen",
                "dosage": "5mg",
                "frequency": "once_daily",
                "duration": "7 days",
                "special_instructions": "Take with food",
                "refills": 2
            }
        }
    ],
    "notes": "Additional instructions",
    "follow_up_date": "2024-04-15"
}
```

#### Patient Model
```php
{
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "address": {
        "address_line_1": "123 Main St",
        "city": "Medical City",
        "state": "MC",
        "postal_code": "12345"
    }
}
```

#### Doctor Model
```php
{
    "id": 1,
    "first_name": "Dr. Jane",
    "last_name": "Smith",
    "meta": {
        "PRC": "123456",
        "PTR": "789012",
        "S2": "345678"
    }
}
```

## Usage

### 1. In Nova Admin Panel
1. Navigate to Medications section
2. Select the medication record
3. Click "Actions" → "Print Prescription PDF"
4. Confirm the action
5. PDF will be generated and downloaded

### 2. Programmatic Usage
```php
use App\Nova\Actions\PrintPrescriptionPDF;

$action = new PrintPrescriptionPDF();
$result = $action->handle($fields, $medications);
```

### 3. Customization
The system automatically uses clinic settings from the database:
- `settings.clinic_name`
- `settings.clinic_address`
- `settings.clinic_city`
- `settings.clinic_state`
- `settings.clinic_zip`
- `settings.clinic_phone`
- `settings.clinic_email`
- `settings.logo`

## Configuration

### 1. Clinic Settings
Configure clinic information through the settings table:
```sql
INSERT INTO settings (key, value) VALUES
('clinic_name', 'Your Clinic Name'),
('clinic_address', '123 Medical Center Dr.'),
('clinic_city', 'Medical City'),
('clinic_state', 'MC'),
('clinic_zip', '12345'),
('clinic_phone', '(555) 123-4567'),
('clinic_email', 'info@yourclinic.com');
```

### 2. Logo Configuration
Upload clinic logo to `storage/app/public/` and set the path in settings:
```sql
INSERT INTO settings (key, value) VALUES ('logo', 'logos/clinic-logo.png');
```

## Styling and Layout

### 1. Color Scheme
- **Primary Blue**: #2563eb (Headers and borders)
- **Success Green**: #10b981 (Refill indicators)
- **Warning Yellow**: #f59e0b (Notes sections)
- **Info Blue**: #0ea5e9 (Pharmacy notes)
- **Success Green**: #22c55e (Validity info)

### 2. Typography
- **Font Family**: Arial, sans-serif
- **Base Font Size**: 12px
- **Headers**: 14px-24px
- **Body Text**: 11px-12px
- **Small Text**: 8px-10px

### 3. Layout
- **Page Size**: A4 (210mm × 297mm)
- **Margins**: 20mm on all sides
- **Grid System**: CSS Grid for patient information
- **Table Layout**: Professional medication table
- **Responsive Design**: Optimized for PDF output

## Error Handling

### 1. Data Validation
- All fields have fallback values (N/A)
- Null-safe operations throughout
- Relationship loading with error handling

### 2. PDF Generation
- Optimized DomPDF settings
- Font subsetting for smaller file sizes
- High DPI (150) for crisp output

## Security Features

### 1. Authentication
- Requires authenticated user
- Doctor relationship validation
- Patient data access control

### 2. Data Protection
- No sensitive data in URLs
- Secure file generation
- Temporary file handling

## Performance Optimization

### 1. Database Queries
- Eager loading of relationships
- Minimal database calls
- Optimized data retrieval

### 2. PDF Generation
- Efficient HTML rendering
- Optimized CSS
- Minimal memory usage

## Troubleshooting

### 1. Common Issues
- **QR Code Not Displaying**: Ensure QrCode package is installed
- **Missing Patient Data**: Check patient relationships
- **PDF Generation Errors**: Verify DomPDF configuration
- **Missing Clinic Info**: Configure settings table

### 2. Debug Steps
1. Check Laravel logs for errors
2. Verify database relationships
3. Test QR code generation separately
4. Validate PDF template syntax

## Future Enhancements

### 1. Planned Features
- Digital signature support
- Multiple language support
- Custom template themes
- Batch PDF generation
- Email integration

### 2. Potential Improvements
- Interactive PDF elements
- Advanced QR code features
- Template customization options
- Integration with pharmacy systems

## Support

For technical support or feature requests:
1. Check the Laravel logs
2. Verify database configuration
3. Test with sample data
4. Contact development team

## License

This feature is part of the MyClinicSoft application and follows the same licensing terms.
