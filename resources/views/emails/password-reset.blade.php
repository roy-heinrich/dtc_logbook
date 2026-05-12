<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <p>Hi {{ $name }},</p>

    <p>
        We received a request to reset the password for your account.
        Click the link below to create a new password:
    </p>

    <p>
        <a href="{{ $resetUrl }}">
            Reset your password
        </a>
    </p>

    <p>
        For your security, this password reset link will expire in
        <strong>{{ $expires }} minutes</strong>.
    </p>

    <p>
        If you did not request a password reset, please ignore this email.
        Your account will remain secure and no changes will be made.
    </p>

    <hr>

    <p style="font-size: 13px; color: #666;">
        This is an automated message. Please do not reply to this email, as replies are not monitored.
    </p>

    <p style="font-size: 13px; color: #666;">
        If you continue to experience issues accessing your account,
        please contact the system administrator or support team.
    </p>

</body>
</html>