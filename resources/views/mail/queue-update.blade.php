<x-mail::message>
# Queue Status Update

Hello {{ $user->name }},

Your queue status has been updated.

## Queue Information:
- **Queue Number:** {{ $queue->queue_number }}
- **Status:** {{ ucfirst(str_replace('_', ' ', $status)) }}
- **Clinic:** {{ $queue->clinic->name }}
- **Doctor:** {{ $queue->doctor ? 'Dr. ' . $queue->doctor->first_name . ' ' . $queue->doctor->last_name : 'Any Available Doctor' }}

@if($status === 'called')
## ⏰ You're Next!
Please proceed to the consultation room. The doctor is ready to see you.

@elseif($status === 'in_progress')
## 👨‍⚕️ Consultation in Progress
Your consultation is currently in progress. Please wait for the doctor to complete your examination.

@elseif($status === 'completed')
## ✅ Consultation Completed
Your consultation has been completed. Thank you for visiting our clinic.

@elseif($status === 'cancelled')
## ❌ Queue Entry Cancelled
Your queue entry has been cancelled. If you need to reschedule, please contact our reception.

@endif

@if($queue->notes)
## Additional Notes:
{{ $queue->notes }}
@endif

<x-mail::button :url="route('queue.status', $queue->id)">
Check Queue Status
</x-mail::button>

If you have any questions, please contact our reception desk.

Best regards,<br>
{{ $queue->clinic->name }} Team
</x-mail::message>
