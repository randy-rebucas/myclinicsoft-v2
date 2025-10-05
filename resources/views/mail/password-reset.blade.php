<x-mail::message>
# Password Reset Request

Hello {{ $user->name }},

We received a request to reset your password for your {{ config('app.name') }} account.

## Reset Your Password:
Click the button below to reset your password. This link will expire in 60 minutes for security reasons.

<x-mail::button :url="$resetUrl">
Reset Password
</x-mail::button>

## Security Information:
- This password reset link is valid for 60 minutes only
- If you didn't request this password reset, please ignore this email
- Your password will not be changed until you click the link above and create a new one

## Manual Reset (if button doesn't work):
Copy and paste this URL into your browser:
{{ $resetUrl }}

## Need Help?
If you're having trouble resetting your password or didn't request this reset, please contact our support team immediately.

**Never share your password reset link with anyone.**

Best regards,<br>
{{ config('app.name') }} Security Team
</x-mail::message>
