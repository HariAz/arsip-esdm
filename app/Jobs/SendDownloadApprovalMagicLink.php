<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\DownloadRequest;
use App\Mail\DownloadApprovalMagicLinkMail;

class SendDownloadApprovalMagicLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $requestId,
        public int $stepOrder
    ) {}

    public function handle(): void
    {
        $downloadRequest = DownloadRequest::with([
            'document.division',
            'requester',
            'steps',
        ])->find($this->requestId);

        if (!$downloadRequest) return;

        $step = $downloadRequest->steps->firstWhere('step_order', $this->stepOrder);

        if (!$step || $step->status !== 'waiting') return;

        // Update status ke 'sent'
        $step->update(['status' => 'sent']);

        Mail::to($step->approver_email)
            ->send(new DownloadApprovalMagicLinkMail($downloadRequest, $step));
    }
}
