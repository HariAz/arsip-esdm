<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\UploadApproval;

class UploadApprovalCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UploadApproval $uploadApproval,
        public string $result  // 'approved' | 'rejected'
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->result === 'approved' ? 'Disetujui' : 'Ditolak';
        return new Envelope(
            subject: "[Sistem Arsip ESDM] Upload Dokumen Sangat Rahasia {$status}: {$this->uploadApproval->document->title}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.upload-approval-completed');
    }
}
