<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $period,
        public string $userName,
        public array $attachmentPaths = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MMG Sales '.ucfirst($this->period).' Report Digest',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-digest',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->attachmentPaths as $path) {
            if (file_exists($path)) {
                $attachments[] = Attachment::fromPath($path);
            }
        }

        return $attachments;
    }
}
