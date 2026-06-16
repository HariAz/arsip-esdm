<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\Division;
use App\Models\DocumentFullText;
use App\Models\UploadApproval;
use App\Models\UploadApprovalStep;
use App\Models\ActivityLog;
use App\Jobs\SendUploadApprovalMagicLink;
use Smalot\PdfParser\Parser;

class DocumentController extends Controller
{
    /**
     * Daftar dokumen + pencarian
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'active');

        $query = Document::with(['division', 'uploader'])->orderByDesc('document_date');

        match($status) {
            'pending'  => $query->where('status', Document::STATUS_PENDING_APPROVAL),
            'rejected' => $query->where('status', Document::STATUS_REJECTED),
            default    => $query->active(),
        };

        // Filter judul / nomor surat
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('document_number', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter tahun
        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        // Filter klasifikasi
        if ($request->filled('classification')) {
            $query->byClassification($request->classification);
        }

        // Full-text search (hanya untuk biasa & terbatas, dan hanya di tab aktif)
        if ($request->filled('fulltext') && $status === 'active') {
            $keyword = $request->fulltext;
            $query->whereHas('fullText', function ($q) use ($keyword) {
                $q->whereRaw(
                    'MATCH(content) AGAINST(? IN BOOLEAN MODE)',
                    [$keyword . '*']
                );
            });
        }

        // Sortable columns
        $sortable = ['document_date', 'document_number', 'title', 'classification'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'document_date';
        $dir  = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $documents = $query->paginate(15)->withQueryString();

        // Catat log pencarian
        if ($request->hasAny(['search', 'year', 'classification', 'fulltext'])) {
            ActivityLog::record(
                action: ActivityLog::ACTION_DOCUMENT_SEARCH,
                userId: Auth::id(),
                description: 'Pencarian dokumen',
                metadata: $request->only(['search', 'year', 'classification', 'fulltext', 'status']),
            );
        }

        // Opsi tahun untuk filter dropdown
        $years = Document::active()->selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');

        // Hitung jumlah per tab
        $counts = [
            'active'  => Document::active()->count(),
            'pending' => Document::where('status', Document::STATUS_PENDING_APPROVAL)->count(),
            'rejected'=> Document::where('status', Document::STATUS_REJECTED)->count(),
        ];

        return view('documents.index', compact('documents', 'years', 'status', 'counts', 'sort', 'dir'));
    }

    /**
     * Form upload dokumen baru
     */
    public function create()
    {
        $divisions = Division::active()->orderBy('name')->get();
        return view('documents.create', compact('divisions'));
    }

    /**
     * Proses upload dokumen
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_number' => ['required', 'string', 'max:100', 'unique:documents,document_number'],
            'title'           => ['required', 'string', 'max:255'],
            'document_date'   => ['required', 'date'],
            'division_id'     => ['required', 'exists:divisions,id'],
            'document_type'                => ['nullable', 'string', 'max:100'],
            'document_classification_code' => ['required', 'string', 'max:50'],
            'classification'               => ['required', 'in:biasa,terbatas,rahasia,sangat_rahasia'],
            'file'            => ['required', 'file', 'mimes:pdf', 'max:20480'], // max 20MB
        ], [
            'document_number.required' => 'Nomor surat wajib diisi.',
            'document_number.unique'   => 'Nomor surat sudah terdaftar di sistem.',
            'title.required'           => 'Perihal / judul wajib diisi.',
            'document_date.required'   => 'Tanggal dokumen wajib diisi.',
            'division_id.required'     => 'Divisi wajib dipilih.',
            'document_classification_code.required' => 'Kode klasifikasi dokumen wajib diisi.',
            'document_classification_code.max'      => 'Kode klasifikasi maksimal 50 karakter.',
            'classification.required'               => 'Klasifikasi wajib dipilih.',
            'file.required'                         => 'File PDF wajib diunggah.',
            'file.mimes'               => 'File harus berformat PDF.',
            'file.max'                 => 'Ukuran file maksimal 20 MB.',
        ]);

        $file     = $request->file('file');
        $division = Division::findOrFail($request->division_id);
        $year     = date('Y', strtotime($request->document_date));

        // Guard: Sangat Rahasia butuh data Bagian Umum yang sudah lengkap
        if ($request->classification === Document::CLASSIFICATION_SANGAT_RAHASIA
            && empty($division->general_affairs_email)) {
            return back()->withInput()->with('error',
                "Data Bagian Umum divisi \"{$division->name}\" belum diisi. " .
                "Perbarui data divisi terlebih dahulu sebelum mengupload dokumen Sangat Rahasia."
            );
        }

        // ── Buat nama file yang aman ──
        $safeTitle    = Str::slug(Str::limit($request->title, 50));
        $uniqueSuffix = Str::random(8);
        $fileName     = "{$safeTitle}_{$uniqueSuffix}.pdf";

        // ── Path penyimpanan: documents/YYYY/KODE-DIVISI/ ──
        $storagePath = "documents/{$year}/{$division->code}";
        $filePath    = $file->storeAs($storagePath, $fileName, 'local');

        // ── Status awal berdasarkan klasifikasi ──
        $status = $request->classification === Document::CLASSIFICATION_SANGAT_RAHASIA
            ? Document::STATUS_PENDING_APPROVAL
            : Document::STATUS_ACTIVE;

        // ── Simpan record dokumen ──
        $document = Document::create([
            'division_id'     => $request->division_id,
            'uploaded_by'     => Auth::id(),
            'document_number' => $request->document_number,
            'title'           => $request->title,
            'document_date'   => $request->document_date,
            'year'            => $year,
            'document_type'                => $request->document_type,
            'document_classification_code' => $request->document_classification_code,
            'classification'               => $request->classification,
            'file_path'       => $filePath,
            'file_name'       => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'file_hash'       => hash_file('sha256', $file->getRealPath()),
            'status'          => $status,
        ]);

        // ── Ekstrak teks PDF (hanya biasa & terbatas) ──
        if ($document->hasFullText()) {
            $this->extractPdfText($document, $filePath);
        }

        // ── Kalau Sangat Rahasia: buat upload approval & kirim magic link ──
        if ($document->classification === Document::CLASSIFICATION_SANGAT_RAHASIA) {
            $this->initiateUploadApproval($document, $division);

            ActivityLog::record(
                action: ActivityLog::ACTION_DOCUMENT_UPLOAD,
                userId: Auth::id(),
                documentId: $document->id,
                description: "Upload dokumen SANGAT RAHASIA menunggu approval: {$document->title}",
            );

            return redirect()->route('documents.index')
                ->with('warning', 'Dokumen berhasil diunggah. Karena klasifikasi "Sangat Rahasia", dokumen menunggu persetujuan sebelum aktif di arsip.');
        }

        // ── Dokumen langsung aktif ──
        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_UPLOAD,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Upload dokumen: {$document->title} [{$document->classification_label}]",
        );

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil diunggah dan telah masuk ke arsip.');
    }

    /**
     * Detail dokumen
     */
    public function show(Document $document)
    {
        if ($document->status === Document::STATUS_PENDING_APPROVAL) {
            return redirect()->route('documents.index')
                ->with('warning', 'Dokumen ini masih menunggu persetujuan upload.');
        }

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_VIEW,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Melihat detail dokumen: {$document->title}",
        );

        $document->load(['division', 'uploader', 'fullText']);
        return view('documents.show', compact('document'));
    }

    /**
     * Halaman preview PDF — menampilkan Blade view dengan iframe
     */
    public function preview(Document $document)
    {
        if (!$document->isPreviewable()) {
            return redirect()->route('documents.show', $document)
                ->with('warning', 'Preview tidak tersedia untuk klasifikasi ' . $document->classification_label . '.');
        }

        if ($document->status !== Document::STATUS_ACTIVE) {
            abort(403, 'Dokumen tidak aktif.');
        }

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_VIEW,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Preview PDF: {$document->title}",
            metadata: ['action_detail' => 'pdf_preview'],
        );

        return view('documents.preview', compact('document'));
    }

    /**
     * Stream byte PDF mentah untuk ditampilkan di iframe (hanya biasa & terbatas)
     */
    public function previewStream(Document $document)
    {
        if (!$document->isPreviewable()) {
            abort(403);
        }

        if ($document->status !== Document::STATUS_ACTIVE) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('local')->path($document->file_path);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }

    /**
     * Download PDF langsung (hanya dokumen klasifikasi biasa)
     */
    public function download(Document $document)
    {
        if (!$document->isFreeDownload()) {
            return redirect()->route('documents.show', $document)
                ->with('warning', 'Dokumen ini memerlukan persetujuan untuk didownload.');
        }

        if ($document->status !== Document::STATUS_ACTIVE) {
            abort(403, 'Dokumen tidak aktif.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        ActivityLog::record(
            action: ActivityLog::ACTION_DOWNLOAD_EXECUTED,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Download dokumen: {$document->title}",
        );

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * Form edit dokumen
     */
    public function edit(Document $document)
    {
        $divisions = Division::active()->orderBy('name')->get();
        return view('documents.edit', compact('document', 'divisions'));
    }

    public function update(Request $request, Document $document)
    {
        if ($document->status === Document::STATUS_PENDING_APPROVAL) {
            return redirect()->route('documents.index')
                ->with('error', 'Dokumen yang masih menunggu approval tidak dapat diedit.');
        }

        $request->validate([
            'title'                        => ['required', 'string', 'max:255'],
            'document_date'                => ['required', 'date'],
            'division_id'                  => ['required', 'exists:divisions,id'],
            'document_type'                => ['nullable', 'string', 'max:100'],
            'document_classification_code' => ['required', 'string', 'max:50'],
        ], [
            'title.required'                        => 'Perihal / judul wajib diisi.',
            'document_date.required'                => 'Tanggal dokumen wajib diisi.',
            'division_id.required'                  => 'Divisi wajib dipilih.',
            'document_classification_code.required' => 'Kode klasifikasi dokumen wajib diisi.',
        ]);

        $document->update([
            'title'                        => $request->title,
            'document_date'                => $request->document_date,
            'year'                         => date('Y', strtotime($request->document_date)),
            'division_id'                  => $request->division_id,
            'document_type'                => $request->document_type,
            'document_classification_code' => $request->document_classification_code,
        ]);

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_UPDATED,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Metadata dokumen diperbarui: {$document->title}",
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Metadata dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        if ($document->status === Document::STATUS_PENDING_APPROVAL) {
            return redirect()->route('documents.index')
                ->with('error', 'Dokumen yang masih menunggu approval tidak dapat dihapus.');
        }

        $title = $document->title;
        $document->delete(); // soft delete

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_DELETED,
            userId: Auth::id(),
            description: "Dokumen diarsipkan (soft delete): {$title}",
        );

        return redirect()->route('documents.index')
            ->with('success', "Dokumen \"{$title}\" berhasil dihapus dari arsip.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        $documents = Document::whereIn('id', $request->ids)
            ->where('status', '!=', Document::STATUS_PENDING_APPROVAL)
            ->get();

        $count = $documents->count();
        foreach ($documents as $doc) {
            $doc->delete();
        }

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_DELETED,
            userId: Auth::id(),
            description: "Bulk delete {$count} dokumen",
            metadata: ['ids' => $request->ids],
        );

        return response()->json(['deleted' => $count]);
    }

    public function trashed()
    {
        $documents = Document::onlyTrashed()
            ->with(['division', 'uploader'])
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('documents.trashed', compact('documents'));
    }

    public function restore(int $id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $document->restore();

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_UPDATED,
            userId: Auth::id(),
            documentId: $document->id,
            description: "Dokumen dipulihkan dari trash: {$document->title}",
        );

        return redirect()->route('documents.trashed')
            ->with('success', "Dokumen \"{$document->title}\" berhasil dipulihkan.");
    }

    public function forceDelete(int $id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $title = $document->title;

        // Hapus file fisik
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->forceDelete();

        ActivityLog::record(
            action: ActivityLog::ACTION_DOCUMENT_DELETED,
            userId: Auth::id(),
            description: "Dokumen dihapus permanen: {$title}",
        );

        return redirect()->route('documents.trashed')
            ->with('success', "Dokumen \"{$title}\" berhasil dihapus permanen.");
    }

    public function uploadApprovalStatus(Document $document)
    {
        if ($document->classification !== Document::CLASSIFICATION_SANGAT_RAHASIA) {
            return redirect()->route('documents.show', $document)
                ->with('info', 'Hanya dokumen Sangat Rahasia yang memiliki status upload approval.');
        }

        $document->load(['division', 'uploader', 'uploadApproval.steps', 'uploadApproval.requester']);
        $uploadApproval = $document->uploadApproval;

        if (!$uploadApproval) {
            return redirect()->route('documents.index')
                ->with('warning', 'Data approval tidak ditemukan untuk dokumen ini.');
        }

        return view('documents.upload-approval-status', compact('document', 'uploadApproval'));
    }

    public function export(Request $request)
    {
        $status = $request->input('status', 'active');

        $query = Document::with(['division', 'uploader'])->orderByDesc('document_date');

        match($status) {
            'pending'  => $query->where('status', Document::STATUS_PENDING_APPROVAL),
            'rejected' => $query->where('status', Document::STATUS_REJECTED),
            default    => $query->active(),
        };

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('document_number', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        if ($request->filled('classification')) {
            $query->byClassification($request->classification);
        }

        $documents = $query->get();

        $statusLabel = match($status) {
            'pending'  => 'menunggu-approval',
            'rejected' => 'ditolak',
            default    => 'aktif',
        };

        $filename = "laporan-arsip-{$statusLabel}-" . date('Ymd-His') . '.csv';

        ActivityLog::record(
            action: 'document.export',
            userId: Auth::id(),
            description: "Export laporan CSV: {$statusLabel} ({$documents->count()} dokumen)",
            metadata: $request->only(['status', 'year', 'classification', 'search']),
        );

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($documents) {
            $handle = fopen('php://output', 'w');
            // BOM supaya Excel baca UTF-8 dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Nomor Surat', 'Perihal / Judul', 'Tanggal Dokumen',
                'Divisi', 'Jenis Dokumen', 'Kode Klasifikasi',
                'Klasifikasi Keamanan', 'Status', 'Ukuran File',
                'Diupload Oleh', 'Tanggal Upload',
            ]);

            foreach ($documents as $i => $doc) {
                fputcsv($handle, [
                    $i + 1,
                    $doc->document_number,
                    $doc->title,
                    $doc->document_date->format('d/m/Y'),
                    $doc->division->name ?? '-',
                    $doc->document_type ?? '-',
                    $doc->document_classification_code ?? '-',
                    $doc->classification_label,
                    match($doc->status) {
                        Document::STATUS_ACTIVE           => 'Aktif',
                        Document::STATUS_PENDING_APPROVAL => 'Menunggu Approval',
                        Document::STATUS_REJECTED         => 'Ditolak',
                        default => $doc->status,
                    },
                    $doc->file_size_formatted,
                    $doc->uploader->name ?? '-',
                    $doc->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ══════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════

    private function extractPdfText(Document $document, string $filePath): void
    {
        try {
            $fullPath = Storage::disk('local')->path($filePath);
            $parser   = new Parser();
            $pdf      = $parser->parseFile($fullPath);
            $text     = $pdf->getText();

            if (!empty(trim($text))) {
                DocumentFullText::updateOrCreate(
                    ['document_id' => $document->id],
                    ['content' => $text, 'extracted_at' => now()]
                );
            }
        } catch (\Exception $e) {
            \Log::warning("Gagal ekstrak teks PDF dokumen ID {$document->id}: " . $e->getMessage());
        }
    }

    private function initiateUploadApproval(Document $document, Division $division): void
    {
        $approval = UploadApproval::create([
            'document_id'  => $document->id,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
            'requested_at' => now(),
        ]);

        // Step 1: Bagian Umum, Step 2: Kepala Divisi
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
            UploadApprovalStep::create([
                'upload_approval_id' => $approval->id,
                'step_order'         => $step['step_order'],
                'approver_name'      => $step['approver_name'],
                'approver_email'     => $step['approver_email'],
                'token'              => Str::random(64),
                'token_expires_at'   => now()->addHours(72),
                'status'             => 'waiting',
            ]);
        }

        // Kirim magic link step 1 via queue
        dispatch(new SendUploadApprovalMagicLink($approval->id, 1));
    }
}
