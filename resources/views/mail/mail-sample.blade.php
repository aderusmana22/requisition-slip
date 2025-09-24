<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Persetujuan Requisition</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        .header {
            background-color: #004a99;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 10px;
            border: 1px solid #eee;
        }
        .details-table td:first-child {
            background-color: #f9f9f9;
            font-weight: bold;
            width: 35%;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            background-color: #28a745;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            background-color: #f4f4f4;
            color: #777;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Requisition Baru Menunggu Persetujuan Anda</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $approver->name }}</strong>,</p>
            <p>
                Anda menerima permintaan persetujuan baru untuk Sample Requisition yang diajukan oleh
                <strong>{{ $requisition->requester->name ?? 'N/A' }}</strong>.
                Berikut adalah rinciannya:
            </p>

            <table class="details-table">
                <tr>
                    <td>No. SRS</td>
                    <td>{{ $requisition->no_srs }}</td>
                </tr>
                <tr>
                    <td>Tanggal Permintaan</td>
                    <td>{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Requester</td>
                    <td>{{ $requisition->requester->name ?? 'N/A' }} ({{ $requisition->requester->nik }})</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>{{ $requisition->customer->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Kategori / Sub Kategori</td>
                    <td>{{ $requisition->category }} / {{ $requisition->sub_category }}</td>
                </tr>
            </table>

            <p>Mohon untuk segera meninjau dan memberikan persetujuan atau penolakan melalui aplikasi.</p>

            <div class="button-container">
                <!-- Arahkan ke halaman approval di aplikasi Anda -->
                <a href="{{ url('/sample-form/approval') }}" class="button">Buka Halaman Approval</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Requisition Slip App. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
