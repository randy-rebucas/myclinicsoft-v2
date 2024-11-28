<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prescription #{{ $medication->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 2cm;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .patient-info {
            margin-bottom: 30px;
        }

        .medication-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .medication-items th,
        .medication-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Prescription</h1>
        <p>Prescription #{{ $medication->id }}</p>
        {{-- <p>Date: {{ $medication->created_at->format('Y-m-d') }}</p> --}}
    </div>

    <div class="patient-info">
        <h2>Patient Information</h2>
        <p>Name: {{ $patient->full_name }}</p>
        <p>ID: {{ $patient->id }}</p>
        <!-- Add more patient details as needed -->
    </div>

    <h2>Prescribed Medications</h2>
    <table class="medication-items">
        <thead>
            <tr>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Frequency</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['fields']['medication_name'] }}</td>
                    <td>{{ $item['fields']['dosage'] }}</td>
                    <td>{{ $item['fields']['frequency'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Doctor's Signature: _____________________</p>
        <p>Date: _____________________</p>
    </div>
</body>

</html>
