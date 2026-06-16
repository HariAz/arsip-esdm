<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\UploadApproval;
use App\Models\UploadApprovalStep;
use App\Models\ActivityLog;

class SendUploadApprovalMagicLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3; // coba kirim ulang 3x kalau gagal

    public function __construct(
        public int $approvalId,
        public int $stepOrder
    ) {}

    public function handle(): void
    {
        $approval = UploadApproval::with(['document.division', 'requester', 'steps'])->find($this->approvalId);

        if (!$approval) return;

        $step = $approval->steps->where('step_order', $this->stepOrder)->first();

        if (!$step || $step->status !== 'waiting') return;

        // Update status step ke 'sent'
        $step->update(['status' => 'sent']);

        // Kirim email
        Mail::send('emails.upload-approval-magic-link', [
            'step'     => $step,
            'approval' => $approval,
            'document' => $approval->document,
        ], function ($mail) use ($step, $approval) {
            $mail->to($step->approver_email, $step->approver_name)
                 ->subject('[Sistem Arsip ESDM] Permohonan Persetujuan Upload Dokumen — ' . $approval->document->title);
        });

        // Catat log
        ActivityLog::record(
            action: ActivityLog::ACTION_UPLOAD_APPROVAL_SENT,
            documentId: $approval->document_id,
            description: "Magic link persetujuan upload dikirim ke: {$step->approver_email} (Step {$this->stepOrder})",
            metadata: [
                'step_order'     => $this->stepOrder,
                'approver_email' => $step->approver_email,
                'expires_at'     => $step->token_expires_at->toISOString(),
            ],
        );
    }
}
