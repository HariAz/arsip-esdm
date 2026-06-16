<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DownloadRequest;

class DownloadApprovalCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DownloadRequest $downloadRequest,
        public string $result  // 'approved' | 'rejected'
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->result === 'approved' ? 'Disetujui' : 'Ditolak';
        return new Envelope(
            subject: "[Sistem Arsip ESDM] Permintaan Download {$status}: {$this->downloadRequest->document->title}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.download-approval-completed');
    }
}
