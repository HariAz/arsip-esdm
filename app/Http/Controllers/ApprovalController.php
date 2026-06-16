<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DownloadApprovalStep;
use App\Models\DownloadRequest;
use App\Models\UploadApprovalStep;
use App\Models\UploadApproval;
use App\Models\Document;
use App\Models\ActivityLog;
use App\Jobs\SendDownloadApprovalMagicLink;
use App\Jobs\SendUploadApprovalMagicLink;
use App\Mail\UploadApprovalCompletedMail;
use App\Mail\DownloadApprovalCompletedMail;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    // ══════════════════════════════════════════
    // DOWNLOAD APPROVAL
    // ══════════════════════════════════════════

    /**
     * Halaman approval download (publik, tanpa login)
     * GET /approve/download/{token}/{action}
     * action = 'approve' atau 'reject'
     */
    public function showDownload(string $token, string $action)
    {
        $step = DownloadApprovalStep::where('token', $token)->firstOrFail();

        // Validasi token
        if ($step->isUsed()) {
            return view('approvals.already-used', ['type' => 'download']);
        }

        if ($step->isExpired()) {
            return view('approvals.expired', ['type' => 'download', 'step' => $step]);
        }

        if ($step->status !== 'sent') {
            return view('approvals.not-ready', ['type' => 'download']);
        }

        if (!in_array($action, ['approve', 'reject'])) {
            abort(404);
        }

        $step->load(['downloadRequest.document.division', 'downloadRequest.steps']);

        // Bukti step sebelumnya (untuk step 2)
        $prevStep = null;
        if ($step->step_order === 2) {
            $prevStep = $step->downloadRequest->steps->firstWhere('step_order', 1);
        }

        return view('approvals.download', compact('step', 'action', 'prevStep'));
    }

    /**
     * Proses keputusan approval download
     * POST /approve/download/{token}/{action}
     */
    public function decideDownload(Request $request, string $token, string $action)
    {
        $step = DownloadApprovalStep::where('token', $token)
            ->with(['downloadRequest.document', 'downloadRequest.steps', 'downloadRequest.requester'])
            ->firstOrFail();

        // Double-check validasi
        if ($step->isUsed() || $step->isExpired() || $step->status !== 'sent') {
            return redirect()->route('approval.download.show', [$token, $action])
                ->with('error', 'Token tidak valid atau sudah digunakan.');
        }

        if (!in_array($action, ['approve', 'reject'])) {
            abort(404);
        }

        $request->validate([
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        $downloadRequest = $step->downloadRequest;
        $document        = $downloadRequest->document;
        $now             = now();

        if ($action === 'approve') {
            // Tandai step ini approved
            $step->update([
                'status'        => 'approved',
                'decided_at'    => $now,
                'token_used_at' => $now,
                'ip_address'    => $request->ip(),
            ]);

            ActivityLog::record(
                action: ActivityLog::ACTION_DOWNLOAD_APPROVED_STEP,
                documentId: $document->id,
                description: "Step {$step->step_order} ({$step->step_label}) disetujui: {$document->title}",
                actorName: $step->approver_name,
                actorEmail: $step->approver_email,
                metadata: ['step' => $step->step_order, 'download_request_id' => $downloadRequest->id],
            );

            // Cek apakah masih ada step selanjutnya
            $nextStep = $downloadRequest->steps
                ->where('step_order', $step->step_order + 1)
                ->first();

            if ($nextStep) {
                // Kirim magic link ke step berikutnya
                dispatch(new SendDownloadApprovalMagicLink($downloadRequest->id, $nextStep->step_order));

                ActivityLog::record(
                    action: ActivityLog::ACTION_DOWNLOAD_MAGIC_SENT,
                    documentId: $document->id,
                    description: "Magic link step {$nextStep->step_order} dikirim ke {$nextStep->approver_email}",
                );

                return view('approvals.success', [
                    'type'    => 'download',
                    'action'  => 'approved',
                    'message' => 'Persetujuan Anda telah dicatat. Magic link telah dikirim ke ' . $nextStep->step_label . ' untuk persetujuan selanjutnya.',
                    'step'    => $step,
                ]);
            } else {
                // Semua step selesai → request approved
                $downloadRequest->update([
                    'status'       => 'approved',
                    'completed_at' => $now,
                ]);

                ActivityLog::record(
                    action: ActivityLog::ACTION_DOWNLOAD_COMPLETED,
                    documentId: $document->id,
                    description: "Semua approval selesai, dokumen siap didownload: {$document->title}",
                );

                // Kirim notifikasi ke arsiparis
                if ($downloadRequest->requester?->email) {
                    Mail::to($downloadRequest->requester->email, $downloadRequest->requester->name)
                        ->send(new DownloadApprovalCompletedMail($downloadRequest, 'approved'));
                }

                return view('approvals.success', [
                    'type'    => 'download',
                    'action'  => 'approved',
                    'message' => 'Semua persetujuan telah diberikan. Arsiparis kini dapat mengunduh dokumen tersebut.',
                    'step'    => $step,
                ]);
            }

        } else {
            // REJECT
            $step->update([
                'status'           => 'rejected',
                'decided_at'       => $now,
                'token_used_at'    => $now,
                'ip_address'       => $request->ip(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            $downloadRequest->update([
                'status'       => 'rejected',
                'completed_at' => $now,
            ]);

            ActivityLog::record(
                action: ActivityLog::ACTION_DOWNLOAD_REJECTED_STEP,
                documentId: $document->id,
                description: "Step {$step->step_order} ditolak: {$document->title}. Alasan: " . ($request->rejection_reason ?? '-'),
                actorName: $step->approver_name,
                actorEmail: $step->approver_email,
            );

            // Kirim notifikasi ke arsiparis
            $downloadRequest->load('steps');
            if ($downloadRequest->requester?->email) {
                Mail::to($downloadRequest->requester->email, $downloadRequest->requester->name)
                    ->send(new DownloadApprovalCompletedMail($downloadRequest, 'rejected'));
            }

            return view('approvals.success', [
                'type'    => 'download',
                'action'  => 'rejected',
                'message' => 'Penolakan Anda telah dicatat. Arsiparis akan mendapat notifikasi.',
                'step'    => $step,
            ]);
        }
    }

    // ══════════════════════════════════════════
    // UPLOAD APPROVAL (Sangat Rahasia)
    // ══════════════════════════════════════════

    /**
     * Halaman approval upload
     * GET /approve/upload/{token}/{action}
     */
    public function showUpload(string $token, string $action)
    {
        $step = UploadApprovalStep::where('token', $token)->firstOrFail();

        if ($step->isUsed()) {
            return view('approvals.already-used', ['type' => 'upload']);
        }

        if ($step->isExpired()) {
            return view('approvals.expired', ['type' => 'upload', 'step' => $step]);
        }

        if ($step->status !== 'sent') {
            return view('approvals.not-ready', ['type' => 'upload']);
        }

        if (!in_array($action, ['approve', 'reject'])) {
            abort(404);
        }

        $step->load(['uploadApproval.document.division', 'uploadApproval.steps']);

        $prevStep = null;
        if ($step->step_order === 2) {
            $prevStep = $step->uploadApproval->steps->firstWhere('step_order', 1);
        }

        return view('approvals.upload', compact('step', 'action', 'prevStep'));
    }

    /**
     * Proses keputusan approval upload
     * POST /approve/upload/{token}/{action}
     */
    public function decideUpload(Request $request, string $token, string $action)
    {
        $step = UploadApprovalStep::where('token', $token)
            ->with(['uploadApproval.document.division', 'uploadApproval.steps', 'uploadApproval.requester'])
            ->firstOrFail();

        if ($step->isUsed() || $step->isExpired() || $step->status !== 'sent') {
            return redirect()->route('approval.upload.show', [$token, $action])
                ->with('error', 'Token tidak valid atau sudah digunakan.');
        }

        if (!in_array($action, ['approve', 'reject'])) {
            abort(404);
        }

        $request->validate([
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        $uploadApproval = $step->uploadApproval;
        $document       = $uploadApproval->document;
        $now            = now();

        if ($action === 'approve') {
            $step->update([
                'status'        => 'approved',
                'decided_at'    => $now,
                'token_used_at' => $now,
                'ip_address'    => $request->ip(),
            ]);

            ActivityLog::record(
                action: ActivityLog::ACTION_UPLOAD_APPROVED,
                documentId: $document->id,
                description: "Step {$step->step_order} upload disetujui: {$document->title}",
                actorName: $step->approver_name,
                actorEmail: $step->approver_email,
            );

            $nextStep = $uploadApproval->steps
                ->where('step_order', $step->step_order + 1)
                ->first();

            if ($nextStep) {
                dispatch(new SendUploadApprovalMagicLink($uploadApproval->id, $nextStep->step_order));

                return view('approvals.success', [
                    'type'    => 'upload',
                    'action'  => 'approved',
                    'message' => 'Persetujuan Anda dicatat. Magic link dikirim ke ' . $nextStep->step_label . '.',
                    'step'    => $step,
                ]);
            } else {
                // Semua step selesai → dokumen jadi aktif
                $uploadApproval->update([
                    'status'       => 'approved',
                    'completed_at' => $now,
                ]);

                $document->update(['status' => Document::STATUS_ACTIVE]);

                ActivityLog::record(
                    action: ActivityLog::ACTION_UPLOAD_APPROVED,
                    documentId: $document->id,
                    description: "Semua approval upload selesai, dokumen aktif: {$document->title}",
                );

                // Kirim notifikasi ke arsiparis
                if ($uploadApproval->requester?->email) {
                    $uploadApproval->refresh()->load(['document.division', 'steps', 'requester']);
                    Mail::to($uploadApproval->requester->email, $uploadApproval->requester->name)
                        ->send(new UploadApprovalCompletedMail($uploadApproval, 'approved'));
                }

                return view('approvals.success', [
                    'type'    => 'upload',
                    'action'  => 'approved',
                    'message' => 'Semua persetujuan diberikan. Dokumen kini aktif dan tersedia di arsip.',
                    'step'    => $step,
                ]);
            }

        } else {
            $step->update([
                'status'           => 'rejected',
                'decided_at'       => $now,
                'token_used_at'    => $now,
                'ip_address'       => $request->ip(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            $uploadApproval->update([
                'status'       => 'rejected',
                'completed_at' => $now,
            ]);

            $document->update(['status' => Document::STATUS_REJECTED]);

            ActivityLog::record(
                action: ActivityLog::ACTION_UPLOAD_REJECTED,
                documentId: $document->id,
                description: "Upload dokumen DITOLAK: {$document->title}",
                actorName: $step->approver_name,
                actorEmail: $step->approver_email,
            );

            // Kirim notifikasi ke arsiparis
            if ($uploadApproval->requester?->email) {
                $uploadApproval->load('steps');
                Mail::to($uploadApproval->requester->email, $uploadApproval->requester->name)
                    ->send(new UploadApprovalCompletedMail($uploadApproval, 'rejected'));
            }

            return view('approvals.success', [
                'type'    => 'upload',
                'action'  => 'rejected',
                'message' => 'Penolakan dicatat. Dokumen tidak akan masuk arsip.',
                'step'    => $step,
            ]);
        }
    }
}
