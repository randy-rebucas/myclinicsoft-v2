# Prescription PDF Generation System

## Overview

This system provides comprehensive prescription PDF generation functionality for medical clinics. It generates professional, legally compliant prescription documents with proper formatting, clinic branding, and all required medical information.

## Features

### ✅ **Core Functionality**
- **Professional PDF Generation**: Creates high-quality, print-ready prescription PDFs
- **Clinic Branding**: Customizable clinic information, logo, and contact details
- **Patient Information**: Complete patient details including address and demographics
- **Medication Details**: Comprehensive medication information with dosage, frequency, and instructions
- **Doctor Information**: Practitioner details with license numbers (PRC, PTR, S2)
- **QR Code Integration**: Patient identification QR codes for verification
- **Legal Compliance**: Proper prescription format meeting medical standards

### ✅ **Data Validation & Error Handling**
- **Input Validation**: Validates all prescription data before PDF generation
- **Error Handling**: Comprehensive error handling with user-friendly messages
- **Data Fallbacks**: Graceful handling of missing or incomplete data
- **Logging**: Detailed logging for troubleshooting and audit trails

### ✅ **Template Features**
- **Responsive Design**: Optimized for both screen and print viewing
- **Professional Layout**: Clean, medical-standard prescription format
- **Customizable Styling**: Professional color scheme and typography
- **Multi-language Support**: UTF-8 encoding for international characters
- **Accessibility**: High contrast and readable fonts

### ✅ **Security & Performance**
- **User Authentication**: Secure access control through Nova
- **Permission-based Access**: Role-based access control
- **Optimized PDF Generation**: Fast, efficient PDF creation
- **Memory Management**: Efficient handling of large prescriptions

## Installation & Setup

### 1. **Dependencies**
Ensure these packages are installed:
```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
```

### 2. **Database Setup**
Run the migrations and seeders:
```bash
php artisan migrate
php artisan db:seed --class=ClinicSettingsSeeder
```

### 3. **Configuration**
The system automatically creates default clinic settings. Customize them through:
- Database settings table
- Environment variables
- Admin panel

## Usage

### **Nova Action Integration**
The prescription PDF generation is available as a Nova action:

```php
// In your Nova resource
public function actions(NovaRequest $request)
{
    return [
        Actions\PrintPrescriptionPDF::make()
            ->onlyOnDetail()
            ->confirmButtonText('Generate PDF')
            ->cancelButtonText('Cancel'),
    ];
}
```

### **Manual Usage**
```php
use App\Nova\Actions\PrintPrescriptionPDF;

$action = new PrintPrescriptionPDF();
$result = $action->handle($fields, collect([$medication]));
```

### **Console Testing**
Test the functionality using the provided console command:
```bash
php artisan test:prescription-pdf
php artisan test:prescription-pdf --user-id=1
```

## Data Structure

### **Required Data**
- **Medication**: Prescription record with items
- **Patient**: Patient information with address
- **Doctor**: Practitioner details with licenses
- **Clinic Settings**: Clinic branding and contact information

### **Prescription Items Format**
```php
[
    'fields' => [
        'medication_name' => 'Medication Name',
        'generic_name' => 'Generic Name',
        'dosage' => 'Dosage Instructions',
        'frequency' => 'Frequency',
        'duration' => 'Duration',
        'special_instructions' => 'Special Instructions',
        'refills' => 0
    ]
]
```

## Configuration

### **Clinic Settings**
The system automatically manages these settings:

| Setting Key | Default Value | Description |
|-------------|---------------|-------------|
| `clinic_name` | Medical Clinic | Clinic name |
| `clinic_address` | 123 Medical Center Dr. | Street address |
| `clinic_city` | Medical City | City |
| `clinic_state` | MC | State/Province |
| `clinic_zip` | 12345 | Postal code |
| `clinic_phone` | (555) 123-4567 | Phone number |
| `clinic_email` | info@medicalclinic.com | Email address |
| `clinic_website` | https://medicalclinic.com | Website URL |
| `clinic_hours` | Monday - Friday: 8:00 AM - 6:00 PM | Operating hours |
| `clinic_emergency` | Emergency: (555) 911-0000 | Emergency contact |

### **PDF Options**
```php
$pdf->setOptions([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,
    'defaultFont' => 'Arial',
    'isPhpEnabled' => true,
    'isFontSubsettingEnabled' => true,
    'defaultCharset' => 'utf-8',
    'dpi' => 150,
    'defaultPaperSize' => 'a4',
    'isJavascriptEnabled' => false,
    'isCssFloatEnabled' => true,
    'isCssPositionEnabled' => true,
]);
```

## Customization

### **Template Customization**
The prescription template is located at `resources/views/pdfs/prescription.blade.php`

**Styling**: Modify the CSS in the `<style>` section
**Layout**: Adjust the HTML structure for different formats
**Branding**: Update clinic information and logo placement

### **Data Customization**
Extend the `prepareTemplateData()` method in `PrintPrescriptionPDF` to:
- Add custom fields
- Modify data formatting
- Include additional information

### **PDF Options**
Customize PDF generation options in the action:
- Paper size and orientation
- Font settings
- DPI and quality
- CSS support options

## Error Handling

### **Common Errors & Solutions**

| Error | Cause | Solution |
|-------|-------|----------|
| "No medication selected" | Empty model collection | Ensure medication exists |
| "Patient not found" | Missing patient relationship | Check patient association |
| "No prescription items" | Empty prescription items | Add medication items |
| "Doctor profile not found" | Missing doctor profile | Create doctor profile |
| "Failed to generate PDF" | PDF generation error | Check logs for details |

### **Logging**
All errors are logged with detailed information:
```php
Log::error('Prescription PDF generation failed: ' . $e->getMessage(), [
    'medication_id' => $medication->id ?? 'unknown',
    'user_id' => Auth::id(),
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

## Testing

### **Automated Tests**
Comprehensive test suite covering:
- Valid data scenarios
- Missing data handling
- Special characters
- Long text content
- Error conditions

Run tests with:
```bash
php artisan test --filter=PrescriptionPDFTest
```

### **Manual Testing**
Use the console command for manual testing:
```bash
php artisan test:prescription-pdf
```

## Troubleshooting

### **PDF Generation Issues**
1. **Check Dependencies**: Ensure DomPDF and QR libraries are installed
2. **Verify Data**: Check that all required data is present
3. **Review Logs**: Check Laravel logs for detailed error messages
4. **Test Template**: Verify template syntax and data binding

### **Performance Issues**
1. **Optimize Images**: Compress clinic logos and images
2. **Reduce Complexity**: Simplify CSS and layout for faster rendering
3. **Memory Limits**: Increase PHP memory limits if needed

### **Styling Issues**
1. **CSS Compatibility**: Ensure CSS is compatible with DomPDF
2. **Font Support**: Use web-safe fonts or embed custom fonts
3. **Layout Testing**: Test with various content lengths

## Security Considerations

### **Access Control**
- User authentication required
- Permission-based access through Nova
- Secure file generation and download

### **Data Protection**
- Input validation and sanitization
- SQL injection prevention
- XSS protection through proper escaping

### **File Security**
- Secure PDF generation
- Temporary file cleanup
- Download security headers

## Performance Optimization

### **PDF Generation**
- Efficient HTML parsing
- Optimized CSS rendering
- Font subsetting for smaller files

### **Memory Management**
- Proper resource cleanup
- Efficient data processing
- Optimized template rendering

## Maintenance

### **Regular Tasks**
- Monitor error logs
- Update clinic information
- Test PDF generation
- Backup settings data

### **Updates**
- Keep dependencies updated
- Test after Laravel updates
- Verify template compatibility

## Support

### **Documentation**
- This README file
- Code comments and PHPDoc
- Test examples

### **Troubleshooting**
- Check Laravel logs
- Review error messages
- Test with console command
- Verify data integrity

## Changelog

### **Version 2.0** (Current)
- ✅ Complete rewrite with comprehensive error handling
- ✅ Data validation and sanitization
- ✅ Professional template design
- ✅ Comprehensive testing suite
- ✅ Console testing command
- ✅ Automatic settings management
- ✅ UTF-8 encoding support
- ✅ Performance optimizations

### **Version 1.0**
- Basic PDF generation
- Simple template
- Limited error handling

## License

This system is part of the MyClinicSoft application and follows the same licensing terms.

---

**Note**: This system is designed for medical use and should be used in compliance with local healthcare regulations and standards.
