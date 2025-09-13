<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prescription</title>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: Menlo, Monaco, Consolas, Courier New, monospace;
        }

        html {
            margin: 1rem
        }

        body.rx-pad {
            height: 11in !important;
            position: relative;
        }

        table.rx-header {
            position: absolute;
            top: 50px;
            width: 100%;
        }

        table.rx-header tr th {
            text-align: left;
            font-size: 26px;
            font-weight: 100;
        }

        table.rx-header tr td {
            font-size: 20px;
            padding: 5px 2px;
        }

        table.rx-body {
            position: absolute;
            top: 200px;
            width: 100%;
        }

        table.rx-body tr td {
            vertical-align: text-top;
            padding: 10px 2px;
            font-size: 24px;
            font-weight: 300;
        }

        table.rx-body tr td:last-child {
            text-align: right;
        }

        table.rx-body tr td span {
            font-style: italic;
            padding-left: 10px;
        }

        table.rx-footer {
            position: absolute;
            top: 790px;
            width: 100%;
        }

        table.rx-footer tr:first-child th {
            text-align: right;
        }

        table.rx-footer tr:nth-child(2) td {
            vertical-align: text-top;
        }

        table.rx-footer tr td {
            font-size: 22px;
            padding: 5px 2px;
        }

        table.rx-footer tr td:last-child {
            text-align: right;
        }

        table.rx-footer tr:nth-child(2) td span {
            font-size: 14px;
        }

        img#watermark {
            position: fixed;
            opacity: 0.3;
            z-index: 99;
            color: white;
            width: 50%;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body class="rx-pad">
    {{-- <img src="{{ $logo }}" alt="brand" id="watermark" /> --}}
    <table class="rx-header">
        <tr>
            <th>Name: </th>
            <th>{{ $patient->name ?? 'N/A' }}</th>
            <td>DOB:</td>
            <td>{{ $patient->birthdate ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Age:</td>
            <td>{{ $patient->age ?? 'N/A' }}</td>
            <td>Gender:</td>
            <td>{{ $patient->gender ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Address:</td>
            <td colspan="3">{{ $patient->address ?? 'N/A' }}</td>
        </tr>
    </table>


    <table class="rx-body">
        @foreach ($prescriptions as $index => $prescription)
            <tr>
                <td>
                    {{ $index + 1 }}
                </td>
                <td>
                    {{ $prescription->title ?? 'N/A' }} <br />
                    <span>Sig: {{ $prescription->description ?? 'N/A' }}</span>
                </td>
                <td>
                    # {{ $prescription->quantity ?? 'N/A' }}
                </td>
            </tr>
        @endforeach
    </table>

    <table class="rx-footer">
        <tr>
            <th colspan="4">{{ $client->owner ?? 'N/A' }}</th>
        </tr>
        <tr>
            <td rowspan="3" colspan="2">
                Patient ID: {{ $patient->id ?? 'N/A' }} <br />
                <span>Use this ID number on your next visit.</span>
                <br />
                @if ($follow_up)
                    <span>Follow up on: {{ $follow_up }}</span>
                @endif
            </td>
            <td>PRC No</td>
            <td>{{ $client->prc ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>PTR No</td>
            <td>{{ $client->ptr ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>S2 No</td>
            <td>{{ $client->s2 ?? 'N/A' }}</td>
        </tr>
    </table>
</body>

</html>
