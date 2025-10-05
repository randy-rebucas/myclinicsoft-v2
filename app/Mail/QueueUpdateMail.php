<?php

namespace App\Mail;

use App\Models\Queue;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Queue $queue,
        public User $user,
        public string $status
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Queue Update - ' . $this->queue->queue_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.queue-update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
