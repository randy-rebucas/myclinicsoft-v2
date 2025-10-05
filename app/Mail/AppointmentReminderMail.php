<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public User $user
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Reminder - ' . $this->appointment->appointment_date->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.appointment-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
