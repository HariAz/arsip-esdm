@extends('layouts.app')

@section('title', 'Trash Dokumen')
@section('page-title', 'Trash Dokumen')

@section('content')
<div class="gold-line"></div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Arsip
        </a>
        <span class="text-muted" style="font-size:13px;">
            {{ $documents->total() }} dokumen di trash
        </span>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-trash"></i> Dokumen Terhapus
        <span class="text-muted fw-normal" style="font-size:12px;">— dapat dipulihkan atau dihapus permanen</span>
    </div>
    <div class="card-body p-0">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-trash d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
                <p class="text-muted mb-0">Tidak ada dokumen di trash.</p>
            </div>
        @else
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Nomor Surat</th>
                            <th>Judul / Perihal</th>
                            <th>Divisi</th>
                            <th>Klasifikasi</th>
                            <th>Dihapus</th>
                            <th style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $i => $doc)
                        <tr>
                            <td class="text-muted">{{ $documents->firstItem() + $i }}</td>
                            <td>
                                <span class="font-monospace" style="font-size:12px;">{{ $doc->document_number }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                                    {{ Str::limit($doc->title, 60) }}
                                </div>
                                @if($doc->document_type)
                                    <small class="text-muted">{{ $doc->document_type }}</small>
                                @endif
                            </td>
                            <td style="font-size:12px;">{{ $doc->division->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_','-',$doc->classification) }}" style="font-size:11px;">
                                    {{ $doc->classification_label }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:12px;">
                                    {{ $doc->deleted_at->translatedFormat('d M Y') }}<br>
                                    <small>{{ $doc->deleted_at->diffForHumans() }}</small>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Restore --}}
                                    <form action="{{ route('documents.restore', $doc->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Pulihkan Dokumen">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    {{-- Hapus Permanen --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Hapus Permanen"
                                            data-doc-id="{{ $doc->id }}"
                                            data-doc-title="{{ Str::limit($doc->title, 50) }}"
                                            onclick="confirmForceDelete(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                    <form id="force-delete-form-{{ $doc->id }}"
                                          action="{{ route('documents.force-delete', $doc->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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

{{-- Modal Konfirmasi Hapus Permanen --}}
<div class="modal fade" id="modalForceDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title d-flex align-items-center gap-2 text-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> Hapus Permanen
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-1" style="font-size:14px;">Dokumen berikut akan dihapus <strong>secara permanen</strong>:</p>
                <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:13px;">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    <span id="modal-force-doc-title" class="fw-semibold"></span>
                </div>
                <p class="text-muted mb-0" style="font-size:12px;">
                    File PDF dan seluruh data dokumen akan dihapus dan <strong>tidak dapat dikembalikan</strong>.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-force-delete">
                    <i class="bi bi-trash3 me-1"></i> Ya, Hapus Permanen
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * { padding: 12px 14px; }
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }
    .pagination { margin: 0; }
    .page-link  { color: var(--esdm-navy); border-color: #d4c9a8; font-size:13px; }
    .page-item.active .page-link { background: var(--esdm-navy); border-color: var(--esdm-navy); }
</style>
@endpush

@push('scripts')
<script>
    let forceDeleteTargetId = null;

    function confirmForceDelete(btn) {
        forceDeleteTargetId = btn.dataset.docId;
        document.getElementById('modal-force-doc-title').textContent = btn.dataset.docTitle;
        new bootstrap.Modal(document.getElementById('modalForceDelete')).show();
    }

    document.getElementById('btn-confirm-force-delete').addEventListener('click', function () {
        if (forceDeleteTargetId) {
            document.getElementById('force-delete-form-' + forceDeleteTargetId).submit();
        }
    });
</script>
@endpush
