<!DOCTYPE html>
<html>
<head>
    <title>Support Query Received</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hi {{ $user->name }},</h2>
    <p>Thank you for reaching out to us. This is an automated reply to confirm that we have received your support query regarding <strong>"{{ $querySubject }}"</strong>.</p>
    <p>Our support team will review your message and get back to you as soon as possible.</p>
    <br>
    <p>Best Regards,</p>
    <p><strong>{{ config('app.name') }} Support Team</strong></p>
</body>
</html>