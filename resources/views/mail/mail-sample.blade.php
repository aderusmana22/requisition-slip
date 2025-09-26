<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Approval Request</title>
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
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 5px;
            color: #ffffff !important;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-review {
            background-color: #007bff;
        }

        .btn-reject {
            background-color: #dc3545;
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
            <h1>Requisition Approval Request</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $approver->name }}</strong>,</p>
            <p>
                A Sample Requisition submitted by
                <strong>{{ $requisition->requester->name ?? 'N/A' }}</strong> requires your approval.
                Here is a summary of the details:
            </p>

            <table class="details-table">
                <tr>
                    <td>SRS No.</td>
                    <td><strong>{{ $requisition->no_srs }}</strong></td>
                </tr>
                <tr>
                    <td>Request Date</td>
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
                    <td>Category / Sub Category</td>
                    <td>{{ $requisition->category }} / {{ $requisition->sub_category }}</td>
                </tr>
                <tr>
                    <td>Current Status</td>
                    <td><strong style="color: #007bff;">{{ $requisition->status }}</strong></td>
                </tr>
            </table>

            <p>Please select one of the actions below to respond to this request.</p>

            <div class="button-container">
                <a href="{{ $approve_url }}" class="button btn-approve" style="color: #ffffff;">Approve</a>
                <a href="{{ $review_url }}" class="button btn-review" style="color: #ffffff;">Approve with Review</a>
                <a href="{{ $reject_url }}" class="button btn-reject" style="color: #ffffff;">Reject</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Requisition Slip App. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
