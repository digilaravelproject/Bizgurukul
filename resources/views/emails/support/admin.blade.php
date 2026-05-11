<!DOCTYPE html>
<html>
<head>
    <title>New Support Query</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>New Support Query Received</h2>
    <p><strong>From:</strong> {{ $user->name }} ({{ $user->email }})</p>
    <p><strong>Subject:</strong> {{ $querySubject }}</p>
    <hr>
    <h3>Message:</h3>
    <p>{!! nl2br(e($queryMessage)) !!}</p>
</body>
</html>