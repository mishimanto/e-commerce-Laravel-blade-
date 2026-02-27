<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $replyMessage;
    public $originalSubject;

    public function __construct($name, $replyMessage, $originalSubject)
    {
        $this->name = $name;
        $this->replyMessage = $replyMessage;
        $this->originalSubject = $originalSubject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to: ' . $this->originalSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
        );
    }
}