<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <p>Hi {{ $name }},</p>
    <p>You requested a password reset. Click the link below to set a new password:</p>
    <p><a href="{{ $resetUrl }}">Reset your password</a></p>
    <p>This link will expire in {{ $expires }} minutes.</p>
    <p>If you did not request a password reset, you can ignore this email.</p>
</body>
</html>
