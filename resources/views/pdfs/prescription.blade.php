<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $prescriptionNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #2c5aa0;
            background: white;
            padding: 0;
        }
        
        .prescription-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }
        
        /* Header Section with Blue Background */
        .header {
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
            padding: 30px 40px;
            border-bottom: 1px solid #b3d9ff;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .stethoscope-logo {
            width: 60px;
            height: 60px;
            margin-right: 20px;
        }
        
        .stethoscope-svg {
            width: 100%;
            height: 100%;
        }
        
        .doctor-info {
            flex: 1;
            text-align: center;
        }
        
        .doctor-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        
        .qualification {
            font-size: 14px;
            color: #2c5aa0;
            margin-bottom: 8px;
        }
        
        .certification-line {
            width: 200px;
            height: 1px;
            background: #2c5aa0;
            margin: 0 auto 5px;
        }
        
        .certification {
            font-size: 12px;
            color: #2c5aa0;
        }
        
        .hospital-info {
            text-align: right;
        }
        
        .hospital-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        
        .hospital-slogan {
            font-size: 12px;
            color: #2c5aa0;
        }
        
        /* Patient Information Section */
        .patient-section {
            background: linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%);
            padding: 20px 40px;
            border-bottom: 1px solid #b3d9ff;
        }
        
        .patient-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            align-items: center;
        }
        
        .patient-field {
            display: flex;
            align-items: center;
        }
        
        .patient-label {
            font-size: 12px;
            color: #2c5aa0;
            margin-right: 10px;
            min-width: 80px;
        }
        
        .patient-input-line {
            flex: 1;
            height: 1px;
            background: #2c5aa0;
            margin-top: 8px;
        }
        
        .patient-value {
            color: #2c5aa0;
            font-weight: 500;
            margin-top: -8px;
        }
        
        /* Prescription Symbol */
        .rx-section {
            padding: 20px 40px;
        }
        
        .rx-symbol {
            font-size: 48px;
            font-weight: bold;
            color: #2c5aa0;
            font-family: 'Times New Roman', serif;
        }
        
        /* Main Content Area */
        .prescription-content {
            padding: 20px 40px;
            min-height: 300px;
        }
        
        .medication-item {
            margin-bottom: 20px;
            font-size: 14px;
            color: #2c5aa0;
        }
        
        .medication-line {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .medication-number {
            font-weight: bold;
            margin-right: 10px;
            min-width: 25px;
        }
        
        .medication-name {
            font-weight: bold;
            margin-right: 15px;
        }
        
        .medication-sig {
            margin-left: 35px;
            margin-bottom: 5px;
        }
        
        .medication-quantity {
            margin-left: 35px;
            font-weight: bold;
        }
        
        /* Footer Section */
        .footer {
            background: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            font-size: 11px;
            color: #2c5aa0;
        }
        
        .contact-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            fill: #2c5aa0;
        }
        
        .decorative-element {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 120px;
            height: 80px;
            background: #2c5aa0;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        
        /* Signature Section */
        .signature-section {
            text-align: right;
        }
        
        .signature-line {
            border-bottom: 2px solid #2c5aa0;
            width: 200px;
            margin-bottom: 5px;
            height: 30px;
        }
        
        .signature-label {
            font-size: 10px;
            color: #2c5aa0;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .prescription-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="prescription-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="stethoscope-logo">
                        <svg class="stethoscope-svg" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                            <!-- Stethoscope forming a heart shape -->
                            <path d="M30 10 C25 5, 15 5, 15 15 C15 20, 20 25, 30 35 C40 25, 45 20, 45 15 C45 5, 35 5, 30 10 Z" 
                                  fill="#2c5aa0" stroke="#2c5aa0" stroke-width="2"/>
                            <!-- Earpieces -->
                            <circle cx="20" cy="8" r="3" fill="#2c5aa0"/>
                            <circle cx="40" cy="8" r="3" fill="#2c5aa0"/>
                            <!-- Tubing -->
                            <path d="M20 8 Q25 12, 30 12 Q35 12, 40 8" 
                                  fill="none" stroke="#2c5aa0" stroke-width="3"/>
                            <!-- Diaphragm -->
                            <circle cx="30" cy="35" r="8" fill="none" stroke="#2c5aa0" stroke-width="2"/>
                            <circle cx="30" cy="35" r="4" fill="#2c5aa0"/>
                        </svg>
                    </div>
                </div>
                
                <div class="doctor-info">
                    <div class="doctor-name">Dr. {{ $doctorInfo['name'] }}</div>
                    <div class="qualification">{{ $doctorInfo['specialization'] ?? 'QUALIFICATION' }}</div>
                    <div class="certification-line"></div>
                    <div class="certification">Certification {{ $doctorInfo['prc'] ?? '12548-20' }}</div>
                </div>
                
                <div class="hospital-info">
                    <div class="hospital-name">{{ $clinic['name'] ?? 'HOSPITAL' }}</div>
                    <div class="hospital-slogan">{{ $clinic['slogan'] ?? 'SLOGAN HERE' }}</div>
                </div>
            </div>
        </div>

        <!-- Patient Information Section -->
        <div class="patient-section">
            <div class="patient-form">
                <div class="patient-field">
                    <span class="patient-label">Patient Name:</span>
                    <div class="patient-input-line"></div>
                    <span class="patient-value">{{ $patientInfo['name'] }}</span>
                </div>
                <div class="patient-field">
                    <span class="patient-label">Date:</span>
                    <div class="patient-input-line"></div>
                    <span class="patient-value">{{ $prescriptionDate }}</span>
                </div>
                <div class="patient-field">
                    <span class="patient-label">Address:</span>
                    <div class="patient-input-line"></div>
                    <span class="patient-value">{{ $patientInfo['address'] }}</span>
                </div>
                <div class="patient-field">
                    <span class="patient-label">Age:</span>
                    <div class="patient-input-line"></div>
                    <span class="patient-value">{{ $patientInfo['age'] }} years</span>
                </div>
                <div class="patient-field" style="grid-column: 1 / -1;">
                    <span class="patient-label">Diagnosis:</span>
                    <div class="patient-input-line"></div>
                    <span class="patient-value">{{ $prescription->diagnosis ?? 'General Consultation' }}</span>
                </div>
            </div>
        </div>

        <!-- Prescription Symbol -->
        <div class="rx-section">
            <div class="rx-symbol">Rx</div>
        </div>

        <!-- Main Content Area -->
        <div class="prescription-content">
            <div class="medication-item">
                <div class="medication-line">
                    <span class="medication-number">1.</span>
                    <span class="medication-name">{{ $medicationInfo['name'] }}</span>
                </div>
                <div class="medication-sig">
                    <strong>Sig:</strong> {{ $medicationInfo['dosage'] }} {{ $medicationInfo['frequency'] }}
                </div>
                <div class="medication-quantity">
                    <strong># {{ $medicationInfo['quantity'] }}</strong>
                </div>
            </div>
            
            @if($medicationInfo['refills'] > 0)
            <div class="medication-item">
                <div class="medication-line">
                    <span class="medication-number">2.</span>
                    <span class="medication-name">Refills</span>
                </div>
                <div class="medication-sig">
                    <strong>Refills:</strong> {{ $medicationInfo['refills'] }} times
                </div>
            </div>
            @endif
            
            @if($instructions && $instructions !== 'Take as directed.')
            <div class="medication-item">
                <div class="medication-line">
                    <span class="medication-number">3.</span>
                    <span class="medication-name">Special Instructions</span>
                </div>
                <div class="medication-sig">
                    <strong>Instructions:</strong> {{ $instructions }}
                </div>
            </div>
            @endif
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="contact-info">
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 16 16">
                        <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.5 12.5a.678.678 0 0 1-.5-.195L4.195 7.5a.678.678 0 0 1-.195-.5l.122-.58a.678.678 0 0 0-.122-.58L2.654 1.328z"/>
                    </svg>
                    <span>{{ $clinic['phone'] ?? '55 47 79 94 15' }}</span>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    <span>{{ $clinic['address'] ?? 'Address Here Number 123' }}</span>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.708 2.825L15 11.105V5.383zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741zM1 11.105l4.708-2.897L1 5.383v5.722z"/>
                    </svg>
                    <span>{{ $clinic['email'] ?? 'email_here@email.com' }}</span>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 16 16">
                        <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
                    </svg>
                    <span>{{ $clinic['website'] ?? 'www.webpage.com' }}</span>
                </div>
            </div>
            
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Physician Signature</div>
            </div>
            
            <div class="decorative-element"></div>
        </div>
    </div>
</body>
</html>