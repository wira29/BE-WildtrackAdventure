{{-- resources/views/emails/transaction-confirmation.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Confirmation</title>
</head>
<body>
    <h2>Transaction Confirmation</h2>
    <p>Dear {{ $name }},</p>

    <p>Your transaction with Order ID <strong>{{ $orderId }}</strong> has been successfully processed.</p>

    <p>Total Amount: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>

    <p>Click the link below to complete your payment:</p>
    <a href="{{ $paymentUrl }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;">
        Complete Payment
    </a>

    <p>Thank you for your purchase!</p>
</body>
</html>
