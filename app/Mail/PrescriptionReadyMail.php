<?php

namespace App\Mail;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrescriptionReadyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Prescription $prescription,
        public User $user
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prescription Ready for Pickup',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.prescription-ready',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
