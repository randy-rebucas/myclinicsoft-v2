<x-mail::message>
# Prescription Ready for Pickup

Hello {{ $user->name }},

Your prescription is ready for pickup at our pharmacy.

## Prescription Details:
- **Medication:** {{ $prescription->medication_name }}
- **Dosage:** {{ $prescription->dosage }}
- **Frequency:** {{ ucfirst(str_replace('_', ' ', $prescription->frequency)) }}
- **Quantity:** {{ $prescription->quantity }}
- **Refills:** {{ $prescription->refills }}
- **Prescribed by:** Dr. {{ $prescription->doctor->first_name }} {{ $prescription->doctor->last_name }}
- **Date:** {{ $prescription->created_at->format('M d, Y') }}

@if($prescription->instructions)
## Instructions:
{{ $prescription->instructions }}
@endif

## Important Information:
- Please bring a valid ID when picking up your prescription
- Prescriptions must be picked up within 7 days
- If you have any questions about your medication, please consult with our pharmacist

## Pharmacy Hours:
Please check our clinic's operating hours for pharmacy pickup times.

<x-mail::button :url="route('prescriptions.show', $prescription->id)">
View Prescription Details
</x-mail::button>

If you have any questions about your prescription, please contact our pharmacy staff.

Best regards,<br>
{{ $prescription->doctor->clinic->name ?? 'Clinic' }} Team
</x-mail::message>
