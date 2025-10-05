<x-mail::message>
# Welcome to {{ config('app.name') }}!

Hello {{ $credentials['name'] ?? 'User' }},

Your account has been created successfully. Here are your login credentials:

## Login Information:
- **Email:** {{ $credentials['email'] }}
- **Username:** {{ $credentials['username'] ?? $credentials['email'] }}
- **Password:** {{ $credentials['password'] }}

## Important Security Notice:
🔒 **Please change your password immediately after your first login for security purposes.**

## Getting Started:
1. Log in to your account using the credentials above
2. Change your password in the account settings
3. Complete your profile information
4. Explore the clinic management features

@if(isset($credentials['login_url']))
<x-mail::button :url="$credentials['login_url']">
Login to Your Account
</x-mail::button>
@endif

If you have any questions or need assistance, please contact our support team.

Welcome aboard!<br>
{{ config('app.name') }} Team
</x-mail::message>
