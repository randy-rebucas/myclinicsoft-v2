<x-mail::message>
# Appointment Reminder

Hello {{ $user->name }},

This is a friendly reminder about your upcoming appointment.

## Appointment Details:
- **Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
- **Duration:** {{ $appointment->duration }} minutes
- **Type:** {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
- **Doctor:** Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
- **Clinic:** {{ $appointment->clinic->name }}

@if($appointment->notes)
## Notes:
{{ $appointment->notes }}
@endif

## Important Reminders:
- Please arrive 15 minutes early for check-in
- Bring a valid ID and insurance card
- If you need to reschedule, please contact us at least 24 hours in advance

<x-mail::button :url="route('appointments.show', $appointment->id)">
View Appointment Details
</x-mail::button>

If you have any questions or need to reschedule, please contact us immediately.

Best regards,<br>
{{ $appointment->clinic->name }} Team
</x-mail::message>
