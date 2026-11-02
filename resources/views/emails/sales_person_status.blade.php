<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color:#333;">
    <h2>Pharmacy System</h2>
    @if ($status === 'approved')
        <p>Hi {{ $user->name }},</p>
        <p>Good news! Your Sales Person account has been <strong style="color:#28a745;">approved</strong> by the admin.</p>
        <p>You can now log in and start using the system.</p>
        <p><a href="{{ route('login') }}">Click here to login</a></p>
    @else
        <p>Hi {{ $user->name }},</p>
        <p>We're sorry to inform you that your Sales Person account registration was <strong style="color:#dc3545;">not approved</strong>.</p>
        <p>If you believe this is a mistake, please contact the system administrator.</p>
    @endif
    <p>Thank you,<br>Pharmacy System Team</p>
</body>
</html>