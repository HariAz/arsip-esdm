<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\DownloadRequest;
use App\Models\DownloadApprovalStep;
use App\Models\ActivityLog;
use App\Jobs\SendDownloadApprovalMagicLink;

class DownloadRequestController extends Controller
{
    /**
     * Daftar semua request download milik arsiparis yang login
     */
    public function index()
    {
        $requests = DownloadRequest::with(['document.division', 'steps'])
            ->where('requested_by', Auth::id())
            ->orderByDesc('requested_at')
            ->paginate(15);

        return view('download-requests.index', compact('requests'));
    }

    /**
     * Form ajukan request download
     */
    public function create(Request $request)
    {
        // Bisa dipanggil dari tombol di halaman dokumen dengan ?document_id=xxx
        $document = null;
        if ($request->filled('document_id')) {
            $document = Document::with('division')->findOrFail($request->document_id);

            // Dokumen biasa tidak perlu request
            if ($document->isFreeDownload()) {
                return redirect()->route('documents.download', $document);
            }

            // Cek apakah sudah ada request pending untuk dokumen ini
            $existing = DownloadRequest::where('document_id', $document->id)
                ->where('requested_by', Auth::id())
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existing) {
                return redirect()->route('download-requests.show', $existing)
                    ->with('warning', 'Anda sudah memiliki permintaan download aktif untuk dokumen ini.');
            }
        }

        return view('download-requests.create', compact('document'));
    }

    /**
     * Simpan request download baru & kirim magic link step 1
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_id' => ['required', 'exists:documents,id'],
            'reason'      => ['nullable', 'string', 'max:500'],
        ], [
            'document_id.required' => 'Dokumen wajib dipilih.',
            'document_id.exists'   => 'Dokumen tidak ditemukan.',
        ]);

        $document = Document::with('division')->findOrFail($request->document_id);

        // Guard: dokumen harus aktif
        if ($document->status !== Document::STATUS_ACTIVE) {
            return back()->with('error', 'Dokumen tidak tersedia untuk didownload.');
        }

        // Guard: dokumen biasa tidak perlu request
        if ($document->isFreeDownload()) {
            return redirect()->route('documents.download', $document);
        }

        // Guard: cek request duplikat
        $existing = DownloadRequest::where('document_id', $document->id)
            ->where('requested_by', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('download-requests.show', $existing)
                ->with('warning', 'Anda sudah memiliki permintaan aktif untuk dokumen ini.');
        }

        // Guard: data Bagian Umum divisi harus sudah diisi
        $division = $document->division;
        if (empty($division->general_affairs_email)) {
            return back()->with('error',
                "Data Bagian Umum divisi \"{$division->name}\" belum diisi. " .
                "Perbarui data divisi terlebih dahulu sebelum mengajukan permintaan download."
            );
        }

        // Buat download request
        $downloadRequest = DownloadRequest::create([
            'document_id'  => $document->id,
            'requested_by' => Auth::id(),
            'reason'       => $request->reason,
            'status'       => 'pending',
            'requested_at' => now(),
        ]);
        $steps = [
            [
                'step_order'     => 1,
                'approver_name'  => $division->general_affairs_name,
                'approver_email' => $division->general_affairs_email,
            ],
            [
                'step_order'     => 2,
                'approver_name'  => $division->head_name,
                'approver_email' => $division->head_email,
            ],
        ];

        foreach ($steps as $step) {
            DownloadApprovalStep::create([
                'download_request_id' => $downloadRequest->id,
                'step_order'          => $step['step_order'],
                'approver_name'       => $step['approver_name'],
                'approver_email'      => $step['approver_email'],
                'token'               => Str::random(64),
                'token_expires_at'    => now()->addHours(72),
                'status'              => 'waiting',
            ]);
        }

        // Kirim magic link step 1
        dispatch(new SendDownloadApprovalMagicLink($downloadRequest->id, 1));

        ActivityLog::record(
            action: ActivityLog::ACTION_DOWNLOAD_REQUESTED,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Mengajukan permintaan download: {$document->title}",
        );

        return redirect()->route('download-requests.show', $downloadRequest)
            ->with('success', 'Permintaan download berhasil diajukan. Magic link telah dikirim ke Bagian Umum untuk persetujuan.');
    }

    /**
     * Detail status sebuah request download
     */
    public function show(DownloadRequest $downloadRequest)
    {
        // Pastikan hanya milik user yang login
        if ($downloadRequest->requested_by !== Auth::id()) {
            abort(403);
        }

        $downloadRequest->load(['document.division', 'steps']);
        return view('download-requests.show', compact('downloadRequest'));
    }

    /**
     * Eksekusi download (hanya jika status approved)
     */
    public function download(DownloadRequest $downloadRequest)
    {
        if ($downloadRequest->requested_by !== Auth::id()) {
            abort(403);
        }

        if (!$downloadRequest->isApproved()) {
            return redirect()->route('download-requests.show', $downloadRequest)
                ->with('error', 'Permintaan download belum disetujui.');
        }

        $document = $downloadRequest->document;

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        // Update status ke downloaded
        $downloadRequest->update([
            'status'        => 'downloaded',
            'downloaded_at' => now(),
        ]);

        ActivityLog::record(
            action: ActivityLog::ACTION_DOWNLOAD_EXECUTED,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Download file: {$document->title}",
            metadata: ['download_request_id' => $downloadRequest->id],
        );

        $fullPath = Storage::disk('local')->path($document->file_path);
        return response()->download($fullPath, $document->file_name);
    }

    /**
     * Request kirim ulang magic link (jika token expired)
     */
    public function resend(DownloadRequest $downloadRequest)
    {
        if ($downloadRequest->requested_by !== Auth::id()) {
            abort(403);
        }

        if ($downloadRequest->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan berstatus pending yang bisa dikirim ulang.');
        }

        $downloadRequest->load('steps');

        // Cari step yang expired
        $expiredStep = $downloadRequest->steps
            ->whereIn('status', ['sent', 'waiting'])
            ->sortBy('step_order')
            ->first();

        if (!$expiredStep) {
            return back()->with('error', 'Tidak ada step yang perlu dikirim ulang.');
        }

        // Reset token dan kirim ulang
        $expiredStep->update([
            'token'            => Str::random(64),
            'token_expires_at' => now()->addHours(72),
            'token_used_at'    => null,
            'status'           => 'waiting',
        ]);

        dispatch(new SendDownloadApprovalMagicLink($downloadRequest->id, $expiredStep->step_order));

        return back()->with('success', 'Magic link berhasil dikirim ulang.');
    }
}
