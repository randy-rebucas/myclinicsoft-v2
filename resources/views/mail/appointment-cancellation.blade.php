<x-mail::message>
# Appointment Cancellation Notice

Hello {{ $user->name }},

Your appointment has been cancelled.

## Cancelled Appointment Details:
- **Date:** {{ $appointment->appointment_date->format('l, F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
- **Doctor:** Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
- **Clinic:** {{ $appointment->clinic->name }}
- **Type:** {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}

## Cancellation Reason:
{{ $reason }}

## Next Steps:
We understand that cancellations can be inconvenient. Here are your options:

1. **Reschedule:** Contact us to book a new appointment
2. **Emergency:** If this is a medical emergency, please visit the nearest emergency room
3. **Questions:** If you have any questions, please don't hesitate to contact us

<x-mail::button :url="route('appointments.create')">
Schedule New Appointment
</x-mail::button>

## Contact Information:
- **Phone:** {{ $appointment->clinic->phone }}
- **Email:** {{ $appointment->clinic->email }}

We apologize for any inconvenience and look forward to serving you in the future.

Best regards,<br>
{{ $appointment->clinic->name }} Team
</x-mail::message>
