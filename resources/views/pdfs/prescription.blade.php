<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Prescription #{{ $medication->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
        }

        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .clinic-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .clinic-details h1 {
            color: #2563eb;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .clinic-details p {
            font-size: 11px;
            margin-bottom: 2px;
            color: #666;
        }

        .prescription-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .prescription-number {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .patient-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .patient-field {
            display: flex;
            align-items: center;
        }

        .patient-field strong {
            min-width: 80px;
            font-size: 11px;
            color: #6b7280;
        }

        .patient-field span {
            font-size: 12px;
            color: #1f2937;
            font-weight: 500;
        }

        .prescription-section {
            margin-bottom: 25px;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
        }

        .prescription-table th {
            background: #2563eb;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
        }

        .prescription-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            vertical-align: top;
        }

        .prescription-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .prescription-table tr:last-child td {
            border-bottom: none;
        }

        .medication-name {
            font-weight: 600;
            color: #1f2937;
        }

        .dosage-info {
            color: #6b7280;
            font-size: 10px;
            margin-top: 3px;
        }

        .refill-indicator {
            display: inline-block;
            background: #10b981;
            color: white;
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
        }

        .footer {
            margin-top: 30px;
            border-top: 2px solid #2563eb;
            padding-top: 20px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .practitioner-info {
            flex: 1;
        }

        .practitioner-name {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .license-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            font-size: 10px;
        }

        .license-info strong {
            color: #6b7280;
            min-width: 60px;
        }

        .license-info span {
            color: #1f2937;
            font-weight: 500;
        }

        .patient-qr-section {
            text-align: center;
            margin-left: 20px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }

        .patient-id {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .qr-note {
            font-size: 8px;
            color: #9ca3af;
            line-height: 1.3;
        }

        .date-signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .date-field {
            text-align: center;
        }

        .date-field strong {
            display: block;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .date-field .underline {
            width: 120px;
            height: 1px;
            background: #000;
            margin: 0 auto;
        }

        .signature-field {
            text-align: center;
        }

        .signature-field strong {
            display: block;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .signature-field .underline {
            width: 150px;
            height: 1px;
            background: #000;
            margin: 0 auto;
        }

        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
        }

        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
        }

        .notes-content {
            font-size: 10px;
            color: #92400e;
            line-height: 1.4;
        }

        .pharmacy-notes {
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 6px;
        }

        .pharmacy-title {
            font-size: 11px;
            font-weight: bold;
            color: #0c4a6e;
            margin-bottom: 8px;
        }

        .pharmacy-content {
            font-size: 10px;
            color: #0c4a6e;
            line-height: 1.4;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(37, 99, 235, 0.03);
            z-index: -1;
            pointer-events: none;
        }

        .validity-info {
            margin-top: 15px;
            padding: 10px;
            background: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 6px;
            text-align: center;
        }

        .validity-text {
            font-size: 10px;
            color: #166534;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="watermark">RX</div>

    <div class="page">
        <div class="header">
            <div class="clinic-info">
                <div class="clinic-details">
                    <h1>{{ config('settings.clinic_name', 'Medical Clinic') }}</h1>
                    <p>{{ config('settings.clinic_address', '123 Medical Center Dr.') }}</p>
                    <p>{{ config('settings.clinic_city', 'Medical City') }}, {{ config('settings.clinic_state', 'MC') }} {{ config('settings.clinic_zip', '12345') }}</p>
                    <p>Phone: {{ config('settings.clinic_phone', '(555) 123-4567') }} | Email: {{ config('settings.clinic_email', 'info@medicalclinic.com') }}</p>
                </div>
                <div class="clinic-logo">
                    @if(config('settings.logo'))
                        <img src="{{ public_path('storage/' . config('settings.logo')) }}" alt="Clinic Logo" style="width: 80px; height: 80px; object-fit: contain;">
                    @endif
                </div>
            </div>

            <div class="prescription-title">PRESCRIPTION</div>
            <div class="prescription-number">Prescription #{{ $medication->id }} | Date: {{ $medication->created_at ? $medication->created_at->format('M d, Y') : 'N/A' }}</div>
        </div>

        <div class="patient-section">
            <div class="section-title">PATIENT INFORMATION</div>
            <div class="patient-grid">
                <div class="patient-field">
                    <strong>Name:</strong>
                    <span>{{ $patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="patient-field">
                    <strong>Patient ID:</strong>
                    <span>{{ $patient->id ?? 'N/A' }}</span>
                </div>
                <div class="patient-field">
                    <strong>Date of Birth:</strong>
                    <span>{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="patient-field">
                    <strong>Age:</strong>
                    <span>{{ $patient->age ?? 'N/A' }}</span>
                </div>
                <div class="patient-field">
                    <strong>Gender:</strong>
                    <span>{{ ucfirst($patient->gender ?? 'N/A') }}</span>
                </div>
                <div class="patient-field">
                    <strong>Address:</strong>
                    <span>
                        @if($patient->address)
                            {{ $patient->address->address_line_1 ?? '' }}
                            @if($patient->address->address_line_2), {{ $patient->address->address_line_2 }}@endif
                            @if($patient->address->city), {{ $patient->address->city }}@endif
                            @if($patient->address->state), {{ $patient->address->state }}@endif
                            @if($patient->address->postal_code), {{ $patient->address->postal_code }}@endif
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="prescription-section">
            <div class="section-title">PRESCRIBED MEDICATIONS</div>
            @if(count($items) > 0)
                <table class="prescription-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 45%;">Medication</th>
                            <th style="width: 20%;">Dosage</th>
                            <th style="width: 30%;">Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="medication-name">
                                        {{ $item['fields']['medication_name'] ?? 'N/A' }}
                                        @if(isset($item['fields']['refills']) && $item['fields']['refills'] > 0)
                                            <span class="refill-indicator">{{ $item['fields']['refills'] }} Refills</span>
                                        @endif
                                    </div>
                                    @if(isset($item['fields']['generic_name']) && $item['fields']['generic_name'])
                                        <div class="dosage-info">Generic: {{ $item['fields']['generic_name'] }}</div>
                                    @endif
                                </td>
                                <td>{{ $item['fields']['dosage'] ?? 'N/A' }}</td>
                                <td>
                                    <div>{{ $item['fields']['frequency'] ?? 'N/A' }}</div>
                                    @if(isset($item['fields']['duration']) && $item['fields']['duration'])
                                        <div class="dosage-info">Duration: {{ $item['fields']['duration'] }}</div>
                                    @endif
                                    @if(isset($item['fields']['special_instructions']) && $item['fields']['special_instructions'])
                                        <div class="dosage-info">{{ $item['fields']['special_instructions'] }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 20px; color: #6b7280; font-style: italic;">
                    No prescription items found
                </div>
            @endif
        </div>

        <div class="validity-info">
            <div class="validity-text">
                This prescription is valid for 30 days from the date of issue.
                Please present this prescription to your pharmacist.
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <div class="practitioner-info">
                    <div class="practitioner-name">{{ $doctor->full_name ?? 'Dr. Practitioner' }}</div>
                    <div class="license-info">
                        <strong>PRC No:</strong>
                        <span>{{ $doctor->meta['PRC'] ?? 'N/A' }}</span>
                        <strong>PTR No:</strong>
                        <span>{{ $doctor->meta['PTR'] ?? 'N/A' }}</span>
                        <strong>S2 No:</strong>
                        <span>{{ $doctor->meta['S2'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="patient-qr-section">
                    <div class="qr-code">
                        {!! QrCode::size(80)->generate($patient->id ?? 'N/A') !!}
                    </div>
                    <div class="patient-id">Patient ID: {{ $patient->id ?? 'N/A' }}</div>
                    <div class="qr-note">Scan this QR code to verify patient identity on your next visit</div>
                </div>
            </div>
        </div>

        <div class="date-signature">
            <div class="date-field">
                <strong>Date Prescribed</strong>
                <div class="underline"></div>
            </div>
            <div class="signature-field">
                <strong>Doctor's Signature</strong>
                <div class="underline"></div>
            </div>
        </div>

        @if(isset($medication->notes) && $medication->notes)
            <div class="notes-section">
                <div class="notes-title">Additional Notes:</div>
                <div class="notes-content">{{ $medication->notes }}</div>
            </div>
        @endif

        @if(isset($medication->follow_up_date) && $medication->follow_up_date)
            <div class="notes-section">
                <div class="notes-title">Follow-up Appointment:</div>
                <div class="notes-content">Please schedule a follow-up appointment for {{ \Carbon\Carbon::parse($medication->follow_up_date)->format('M d, Y') }}</div>
            </div>
        @endif

        <div class="pharmacy-notes">
            <div class="pharmacy-title">Pharmacy Instructions:</div>
            <div class="pharmacy-content">
                • Please ensure proper patient identification before dispensing<br>
                • Check for drug interactions and allergies<br>
                • Provide patient counseling on medication use<br>
                • Store medications according to manufacturer guidelines
            </div>
        </div>
    </div>
</body>
</html>
