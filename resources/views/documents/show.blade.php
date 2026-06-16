@extends('layouts.app')

@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Arsip Dokumen</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($document->document_number, 40) }}</li>
@endsection

@section('content')
<div class="gold-line"></div>

<div class="row g-4">

    {{-- ── KOLOM KIRI: Info Dokumen ── --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i>
                    Informasi Dokumen
                </span>
                <span class="badge badge-{{ str_replace('_','-',$document->classification) }} px-2 py-1">
                    {{ $document->classification_label }}
                </span>
            </div>
            <div class="card-body">
                <dl class="row doc-info">
                    <dt class="col-sm-4">Nomor Surat</dt>
                    <dd class="col-sm-8 font-monospace">{{ $document->document_number }}</dd>

                    <dt class="col-sm-4">Perihal</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $document->title }}</dd>

                    <dt class="col-sm-4">Tanggal Dokumen</dt>
                    <dd class="col-sm-8">{{ $document->document_date->translatedFormat('d F Y') }}</dd>

                    <dt class="col-sm-4">Tahun Arsip</dt>
                    <dd class="col-sm-8">{{ $document->year }}</dd>

                    <dt class="col-sm-4">Divisi</dt>
                    <dd class="col-sm-8">{{ $document->division->name ?? '-' }}</dd>

                    <dt class="col-sm-4">Jenis Dokumen</dt>
                    <dd class="col-sm-8">{{ $document->document_type ?? '—' }}</dd>

                    <dt class="col-sm-4">Kode Klasifikasi</dt>
                    <dd class="col-sm-8">
                        <span class="font-monospace badge bg-light text-dark border" style="font-size:12px; letter-spacing:.5px;">
                            {{ $document->document_classification_code ?: '—' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if($document->status === 'active')
                            <span class="badge bg-success">Aktif</span>
                        @elseif($document->status === 'pending_approval')
                            <span class="badge bg-warning text-dark">Menunggu Approval</span>
                        @elseif($document->status === 'rejected')
                            <span class="badge bg-danger">Ditolak</span>
                        @else
                            <span class="badge bg-secondary">Diarsipkan</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN: File & Aksi ── --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf"></i> File Dokumen
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size:2.5rem; flex-shrink:0;"></i>
                    <div>
                        <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy); word-break:break-all;">
                            {{ $document->file_name }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:12px;">
                            Ukuran: {{ $document->file_size_formatted }}
                        </div>
                        <div class="text-muted" style="font-size:11px;">
                            Diunggah: {{ $document->created_at->translatedFormat('d M Y, H:i') }}
                            oleh {{ $document->uploader->name ?? '-' }}
                        </div>
                    </div>
                </div>

                @if($document->isPreviewable())
                    <a href="{{ route('documents.preview', $document) }}"
                       class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-eye me-2"></i>Preview PDF
                    </a>
                @else
                    <div class="alert alert-secondary py-2 px-3 mb-2" style="font-size:12px;">
                        <i class="bi bi-eye-slash me-1"></i>
                        Preview tidak tersedia untuk klasifikasi <strong>{{ $document->classification_label }}</strong>.
                    </div>
                @endif

                @if($document->isFreeDownload())
                    <a href="{{ route('documents.download', $document) }}"
                       class="btn btn-success w-100">
                        <i class="bi bi-download me-2"></i>Download PDF
                    </a>
                @else
                    <a href="{{ route('download-requests.create', ['document_id' => $document->id]) }}"
                       class="btn btn-warning w-100">
                        <i class="bi bi-send me-2"></i>Ajukan Permintaan Download
                    </a>
                    <p class="text-muted mt-2 mb-0" style="font-size:11px; text-align:center;">
                        Memerlukan persetujuan sebelum dapat didownload.
                    </p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="font-size:13px;">
                <i class="bi bi-info-circle me-1"></i> Metadata Sistem
            </div>
            <div class="card-body p-3">
                <dl class="row doc-info mb-0" style="font-size:12px;">
                    <dt class="col-5 text-muted fw-normal">SHA-256</dt>
                    <dd class="col-7 font-monospace" style="font-size:10px; word-break:break-all;">
                        {{ $document->file_hash ? substr($document->file_hash,0,16).'...' : '—' }}
                    </dd>
                    <dt class="col-5 text-muted fw-normal">Full-text Index</dt>
                    <dd class="col-7">
                        @if($document->fullText)
                            <span class="badge bg-success">Tersedia</span>
                        @else
                            <span class="badge bg-secondary">Tidak tersedia</span>
                        @endif
                    </dd>
                    <dt class="col-5 text-muted fw-normal">Diperbarui</dt>
                    <dd class="col-7">{{ $document->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>
@endsection

@push('styles')
<style>
    .doc-info dt { font-weight:600; font-size:13px; color:#6b7280; }
    .doc-info dd { font-size:13px; color:var(--esdm-navy); margin-bottom:10px; }
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }
</style>
@endpush
