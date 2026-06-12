<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DownloadRequest;
use App\Models\DownloadApprovalStep;

class DownloadApprovalMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DownloadRequest $downloadRequest,
        public DownloadApprovalStep $step
    ) {}

    public function envelope(): Envelope
    {
        $docTitle = $this->downloadRequest->document->title;
        return new Envelope(
            subject: "[Persetujuan Download] {$docTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.download-approval-magic-link',
        );
    }
}
