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

{{-- ── TABS STATUS ── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <ul class="nav nav-pills gap-1" style="font-size:13px;">
        <li class="nav-item">
            <a class="nav-link py-1 px-3 {{ $status === 'active' ? 'active' : '' }}"
               href="{{ route('documents.index', array_merge(request()->except(['status','page']), ['status'=>'active'])) }}">
                <i class="bi bi-folder2-open me-1"></i>Aktif
                <span class="badge ms-1 {{ $status === 'active' ? 'bg-white text-primary' : 'bg-success' }}">{{ $counts['active'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1 px-3 {{ $status === 'pending' ? 'active' : '' }}"
               href="{{ route('documents.index', array_merge(request()->except(['status','page']), ['status'=>'pending'])) }}">
                <i class="bi bi-hourglass-split me-1"></i>Menunggu Approval
                @if($counts['pending'] > 0)
                    <span class="badge ms-1 {{ $status === 'pending' ? 'bg-white text-warning' : 'bg-warning text-dark' }}">{{ $counts['pending'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1 px-3 {{ $status === 'rejected' ? 'active' : '' }}"
               href="{{ route('documents.index', array_merge(request()->except(['status','page']), ['status'=>'rejected'])) }}">
                <i class="bi bi-x-circle me-1"></i>Ditolak
                @if($counts['rejected'] > 0)
                    <span class="badge ms-1 {{ $status === 'rejected' ? 'bg-white text-danger' : 'bg-danger' }}">{{ $counts['rejected'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1 px-3 text-muted"
               href="{{ route('documents.trashed') }}">
                <i class="bi bi-trash me-1"></i>Trash
            </a>
        </li>
    </ul>
    <div class="d-flex gap-2">
        <a href="{{ route('documents.export', request()->only(['status','year','classification','search'])) }}"
           class="btn btn-outline-secondary btn-sm" title="Export CSV dengan filter saat ini">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('documents.create') }}" class="btn btn-gold btn-sm">
            <i class="bi bi-cloud-upload me-1"></i> Upload Dokumen
        </a>
    </div>
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
                            <th style="width:90px;">Aksi</th>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:180px; font-size:13px;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('documents.show', $doc) }}">
                                                <i class="bi bi-eye me-2 text-secondary"></i>Detail
                                            </a>
                                        </li>

                                        @if($doc->isPreviewable())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('documents.preview', $doc) }}">
                                                <i class="bi bi-filetype-pdf me-2 text-primary"></i>Preview PDF
                                            </a>
                                        </li>
                                        @endif

                                        @if($doc->isFreeDownload())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('documents.download', $doc) }}">
                                                <i class="bi bi-download me-2 text-success"></i>Download
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('download-requests.create', ['document_id' => $doc->id]) }}">
                                                <i class="bi bi-send me-2 text-warning"></i>Ajukan Download
                                            </a>
                                        </li>
                                        @endif

                                        @if($doc->status !== 'pending_approval')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('documents.edit', $doc) }}">
                                                    <i class="bi bi-pencil me-2 text-info"></i>Edit Metadata
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger"
                                                        type="button"
                                                        data-doc-id="{{ $doc->id }}"
                                                        data-doc-title="{{ Str::limit($doc->title, 50) }}"
                                                        onclick="confirmDelete(this)">
                                                    <i class="bi bi-trash me-2"></i>Hapus
                                                </button>
                                            </li>
                                            <form id="delete-form-{{ $doc->id }}"
                                                  action="{{ route('documents.destroy', $doc) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('documents.upload-approval', $doc) }}">
                                                    <i class="bi bi-diagram-3 me-2 text-warning"></i>Lihat Status Approval
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
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

{{-- ── MODAL KONFIRMASI HAPUS ── --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title d-flex align-items-center gap-2 text-danger" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus Dokumen
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-1" style="font-size:14px;">Dokumen berikut akan dihapus dari arsip:</p>
                <div class="alert alert-danger py-2 px-3 mb-0" style="font-size:13px;">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    <span id="modal-doc-title" class="fw-semibold"></span>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                    Dokumen akan diarsipkan (soft delete) dan tidak akan muncul di daftar. Tindakan ini dapat dikembalikan oleh administrator.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete">
                    <i class="bi bi-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
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
    .table-responsive { overflow: visible; }
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

@push('scripts')
<script>
    let deleteTargetId = null;

    function confirmDelete(btn) {
        deleteTargetId = btn.dataset.docId;
        document.getElementById('modal-doc-title').textContent = btn.dataset.docTitle;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    }

    document.getElementById('btn-confirm-delete').addEventListener('click', function () {
        if (deleteTargetId) {
            document.getElementById('delete-form-' + deleteTargetId).submit();
        }
    });
</script>
@endpush
