<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\UploadApprovalStep;
use App\Models\ActivityLog;
use App\Jobs\SendUploadApprovalMagicLink;

class ResendExpiredApprovalLinks extends Command
{
    protected $signature   = 'arsip:resend-expired-links {--dry-run : Lihat daftar tanpa kirim ulang}';
    protected $description = 'Regenerasi token dan kirim ulang magic link approval upload yang sudah kadaluwarsa';

    public function handle(): int
    {
        $expiredSteps = UploadApprovalStep::where('status', 'sent')
            ->where('token_expires_at', '<', now())
            ->with(['uploadApproval.document', 'uploadApproval.requester'])
            ->get();

        if ($expiredSteps->isEmpty()) {
            $this->info('Tidak ada token kadaluwarsa yang perlu dikirim ulang.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$expiredSteps->count()} token kadaluwarsa:");

        $rows = [];
        foreach ($expiredSteps as $step) {
            $approval = $step->uploadApproval;
            if (!$approval || $approval->status !== 'pending') continue;

            $rows[] = [
                $step->id,
                $approval->document->document_number ?? '-',
                $step->step_label,
                $step->approver_email,
                $step->token_expires_at->format('d/m/Y H:i'),
            ];
        }

        $this->table(
            ['Step ID', 'Nomor Surat', 'Step', 'Approver Email', 'Kadaluwarsa'],
            $rows
        );

        if ($this->option('dry-run')) {
            $this->warn('Mode dry-run: tidak ada token yang dikirim ulang.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Kirim ulang {$expiredSteps->count()} magic link?", true)) {
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($expiredSteps as $step) {
            $approval = $step->uploadApproval;
            if (!$approval || $approval->status !== 'pending') continue;

            // Regenerasi token dengan expiry baru 72 jam
            $step->update([
                'token'            => Str::random(64),
                'token_expires_at' => now()->addHours(72),
                'token_used_at'    => null,
                'status'           => 'waiting',
            ]);

            // Kirim ulang magic link
            dispatch(new SendUploadApprovalMagicLink($approval->id, $step->step_order));

            ActivityLog::record(
                action: ActivityLog::ACTION_UPLOAD_APPROVAL_SENT,
                documentId: $approval->document_id,
                description: "Token kadaluwarsa — magic link dikirim ulang ke: {$step->approver_email} (Step {$step->step_order})",
                metadata: [
                    'step_order'     => $step->step_order,
                    'approver_email' => $step->approver_email,
                    'expires_at'     => now()->addHours(72)->toISOString(),
                    'reason'         => 'token_expired_resend',
                ],
            );

            $this->line("  ✓ Dikirim ulang ke <info>{$step->approver_email}</info> (Step {$step->step_order}, Dok: {$approval->document->document_number})");
            $sent++;
        }

        $this->newLine();
        $this->info("Selesai. {$sent} magic link berhasil dikirim ulang.");
        return self::SUCCESS;
    }
}
