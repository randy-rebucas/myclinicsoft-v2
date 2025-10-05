<x-mail::message>
# Application Update Notification

Hello {{ $updateData['user_name'] ?? 'User' }},

{{ $updateData['message'] ?? 'We have an important update to share with you about our clinic management system.' }}

@if(isset($updateData['features']))
## New Features:
@foreach($updateData['features'] as $feature)
- {{ $feature }}
@endforeach
@endif

@if(isset($updateData['improvements']))
## Improvements:
@foreach($updateData['improvements'] as $improvement)
- {{ $improvement }}
@endforeach
@endif

@if(isset($updateData['action_url']))
<x-mail::button :url="$updateData['action_url']">
{{ $updateData['action_text'] ?? 'View Update' }}
</x-mail::button>
@endif

If you have any questions or need assistance, please don't hesitate to contact our support team.

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
