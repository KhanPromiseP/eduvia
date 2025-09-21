<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $payment->transaction_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .invoice-details { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; font-size: 16px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Transaction #{{ $payment->transaction_id }}</p>
        <p>Date: {{ $payment->completed_at->format('M d, Y') }}</p>
    </div>

    <div class="invoice-details">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>From:</strong><br>
                    {{ config('app.name') }}<br>
                    Education Platform<br>
                    {{ config('app.url') }}
                </td>
                <td width="50%">
                    <strong>To:</strong><br>
                    {{ $payment->user->name }}<br>
                    {{ $payment->user->email }}<br>
                    Customer since: {{ $payment->user->created_at->format('M d, Y') }}
                </td>
            </tr>
        </table>
    </div>

    <h3>Course Details</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->course->title }}</td>
                <td>{{ $payment->formatted_amount }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Payment Summary</h3>
    <table class="table">
        <tr>
            <td>Course Price:</td>
            <td>{{ $payment->formatted_amount }}</td>
        </tr>
        <tr>
            <td>Tax:</td>
            <td>$0.00</td>
        </tr>
        <tr class="total">
            <td>Total:</td>
            <td>{{ $payment->formatted_amount }}</td>
        </tr>
    </table>

    <h3>Transaction Details</h3>
    <table class="table">
        <tr>
            <td>Transaction ID:</td>
            <td>{{ $payment->transaction_id }}</td>
        </tr>
        <tr>
            <td>Payment Date:</td>
            <td>{{ $payment->completed_at->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td>Payment Method:</td>
            <td>{{ $payment->payment_method_display }}</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td>Completed</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Need help? Contact support: support@example.com</p>
        <p>Invoice generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>