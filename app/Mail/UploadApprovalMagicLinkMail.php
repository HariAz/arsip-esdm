<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\UploadApproval;
use App\Models\UploadApprovalStep;

class UploadApprovalMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UploadApproval $approval,
        public UploadApprovalStep $step
    ) {}

    public function envelope(): Envelope
    {
        $docTitle = $this->approval->document->title;
        return new Envelope(
            subject: "[Persetujuan Diperlukan] Upload Dokumen: {$docTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.upload-approval-magic-link',
        );
    }
}
