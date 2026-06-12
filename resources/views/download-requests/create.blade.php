@extends('layouts.app')

@section('title', 'Ajukan Permintaan Download')
@section('page-title', 'Ajukan Permintaan Download')

@section('content')
<div class="gold-line"></div>

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-send"></i>
        Form Permintaan Download
    </div>
    <div class="card-body p-4">

        @if($document)
        {{-- ── INFO DOKUMEN ── --}}
        <div class="card mb-4" style="border-color:var(--esdm-border); background:#faf8f3;">
            <div class="card-body py-3">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size:2rem; flex-shrink:0;"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold" style="font-size:14px; color:var(--esdm-navy);">
                            {{ $document->title }}
                        </div>
                        <div class="font-monospace text-muted mt-1" style="font-size:12px;">
                            {{ $document->document_number }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                            <span class="badge badge-{{ str_replace('_', '-', $document->classification) }} px-2 py-1" style="font-size:11px;">
                                {{ $document->classification_label }}
                            </span>
                            <span class="text-muted" style="font-size:12px;">
                                <i class="bi bi-building me-1"></i>{{ $document->division->name ?? '-' }}
                            </span>
                            <span class="text-muted" style="font-size:12px;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $document->document_date->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INFO ALUR APPROVAL ── --}}
        <div class="alert alert-info d-flex gap-2 mb-4" style="font-size:13px;">
            <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
            <div>
                Permintaan ini akan melalui persetujuan <strong>2 tahap</strong>:
                <ol class="mb-0 mt-1 ps-3">
                    <li>Magic link dikirim ke <strong>Bagian Umum {{ $document->division->name ?? '' }}</strong></li>
                    <li>Setelah disetujui, magic link dikirim ke <strong>Kepala Divisi</strong></li>
                </ol>
                Anda dapat memantau status di halaman "Permintaan Download".
            </div>
        </div>

        <form method="POST" action="{{ route('download-requests.store') }}">
            @csrf
            <input type="hidden" name="document_id" value="{{ $document->id }}">

            {{-- ── ALASAN DOWNLOAD ── --}}
            <div class="mb-4">
                <label class="form-label" style="font-size:13px; font-weight:600; color:var(--esdm-navy);">
                    Alasan Download
                    <span class="text-muted fw-normal">(opsional)</span>
                </label>
                <textarea
                    name="reason"
                    class="form-control @error('reason') is-invalid @enderror"
                    rows="3"
                    placeholder="Jelaskan keperluan download dokumen ini (opsional)..."
                    style="font-size:14px;"
                >{{ old('reason') }}</textarea>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted" style="font-size:11px;">
                    Alasan ini akan ditampilkan kepada approver untuk membantu pengambilan keputusan.
                </small>
            </div>

            {{-- ── TOMBOL ── --}}
            <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i> Ajukan Permintaan
                </button>
            </div>
        </form>

        @else
        {{-- ── TIDAK ADA DOKUMEN TERPILIH ── --}}
        <div class="text-center py-5">
            <i class="bi bi-exclamation-circle d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
            <p class="text-muted mb-3">Tidak ada dokumen yang dipilih.</p>
            <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-folder2-open me-1"></i> Pilih dari Daftar Arsip
            </a>
        </div>
        @endif

    </div>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }
</style>
@endpush
