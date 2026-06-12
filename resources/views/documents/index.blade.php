@extends('layouts.app')

@section('title', 'Arsip Dokumen')
@section('page-title', 'Arsip Dokumen')

@section('content')
<div class="gold-line"></div>

{{-- ── PANEL PENCARIAN ── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-search"></i> Pencarian & Filter Dokumen
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('documents.index') }}" id="search-form">
            <div class="row g-3">

                {{-- Judul / Nomor Surat --}}
                <div class="col-md-4">
                    <label class="form-label-sm">Judul / Nomor Surat</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-file-earmark-text text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0"
                            placeholder="Cari judul atau nomor surat..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                {{-- Tahun --}}
                <div class="col-md-2">
                    <label class="form-label-sm">Tahun</label>
                    <select name="year" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Klasifikasi --}}
                <div class="col-md-2">
                    <label class="form-label-sm">Klasifikasi</label>
                    <select name="classification" class="form-select">
                        <option value="">Semua</option>
                        <option value="biasa"          {{ request('classification') === 'biasa'          ? 'selected' : '' }}>Biasa</option>
                        <option value="terbatas"       {{ request('classification') === 'terbatas'       ? 'selected' : '' }}>Terbatas</option>
                        <option value="rahasia"        {{ request('classification') === 'rahasia'        ? 'selected' : '' }}>Rahasia</option>
                        <option value="sangat_rahasia" {{ request('classification') === 'sangat_rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
                    </select>
                </div>

                {{-- Full-text search --}}
                <div class="col-md-3">
                    <label class="form-label-sm">
                        Cari Isi Dokumen
                        <span class="text-muted" style="font-size:10px; font-weight:400;">(hanya Biasa & Terbatas)</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-file-earmark-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="fulltext"
                            class="form-control border-start-0"
                            placeholder="Kata kunci dalam isi PDF..."
                            value="{{ request('fulltext') }}"
                        >
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" title="Cari">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->hasAny(['search','year','classification','fulltext']))
                        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary w-100" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ── HEADER TABEL ── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <span class="fw-semibold" style="font-size:14px; color:var(--esdm-navy)">
            <i class="bi bi-folder2-open me-1"></i>
            Daftar Dokumen
        </span>
        <span class="text-muted ms-2" style="font-size:13px;">
            — {{ $documents->total() }} dokumen ditemukan
        </span>
    </div>
    <a href="{{ route('documents.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-cloud-upload me-1"></i> Upload Dokumen
    </a>
</div>

{{-- ── TABEL DOKUMEN ── --}}
<div class="card">
    <div class="card-body p-0">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-folder2 d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
                <p class="text-muted mb-1">Tidak ada dokumen ditemukan.</p>
                @if(request()->hasAny(['search','year','classification','fulltext']))
                    <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian
                    </a>
                @else
                    <a href="{{ route('documents.create') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Upload Dokumen Pertama
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Nomor Surat</th>
                            <th>Judul / Perihal</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>Klasifikasi</th>
                            <th>Ukuran</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $i => $doc)
                        <tr>
                            <td class="text-muted">{{ $documents->firstItem() + $i }}</td>

                            <td>
                                <span class="font-monospace" style="font-size:12px;">
                                    {{ $doc->document_number }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                                    {{ Str::limit($doc->title, 60) }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                    @if($doc->document_type)
                                        <small class="text-muted">{{ $doc->document_type }}</small>
                                    @endif
                                    @if($doc->document_classification_code)
                                        <span class="font-monospace badge bg-light text-secondary border" style="font-size:10px;">
                                            {{ $doc->document_classification_code }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span style="font-size:12px;">{{ $doc->division->name ?? '-' }}</span>
                            </td>

                            <td>
                                <span style="font-size:12px;">
                                    {{ $doc->document_date->translatedFormat('d M Y') }}
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $doc->classification) }} px-2 py-1" style="font-size:11px;">
                                    {{ $doc->classification_label }}
                                </span>
                            </td>

                            <td>
                                <span class="text-muted" style="font-size:12px;">
                                    {{ $doc->file_size_formatted }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('documents.show', $doc) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Detail Dokumen">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Tombol Preview (hanya biasa & terbatas) --}}
                                    @if($doc->isPreviewable())
                                        <a href="{{ route('documents.preview', $doc) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank"
                                           title="Preview PDF">
                                            <i class="bi bi-filetype-pdf"></i>
                                        </a>
                                    @endif

                                    {{-- Tombol Download --}}
                                    @if($doc->isFreeDownload())
                                        <a href="{{ route('documents.download', $doc) }}"
                                           class="btn btn-sm btn-outline-success"
                                           title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('download-requests.create', ['document_id' => $doc->id]) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Ajukan Download">
                                            <i class="bi bi-send"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($documents->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <small class="text-muted">
                        Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }}
                        dari {{ $documents->total() }} dokumen
                    </small>
                    {{ $documents->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .form-label-sm {
        font-size: 12px;
        font-weight: 600;
        color: var(--esdm-navy);
        margin-bottom: 5px;
        display: block;
    }
    .table > :not(caption) > * > * { padding: 12px 14px; }
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }

    /* Pagination styling */
    .pagination { margin: 0; }
    .page-link  { color: var(--esdm-navy); border-color: #d4c9a8; font-size:13px; }
    .page-item.active .page-link {
        background: var(--esdm-navy);
        border-color: var(--esdm-navy);
    }
</style>
@endpush
