<x-mail::message>
    # Your Account Credentials

    Here are your login credentials:

    Username: {{ $credentials['username'] }}
    Email: {{ $credentials['email'] }}
    Password: {{ $credentials['password'] }}

    Please change your password after your first login.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
