@extends('layouts.app')
@section('title', 'Manajemen Divisi')
@section('page-title', 'Manajemen Divisi')

@section('content')
<div class="gold-line"></div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <span class="fw-semibold" style="font-size:14px; color:var(--esdm-navy);">
            <i class="bi bi-building me-1"></i>Daftar Divisi
        </span>
        <span class="text-muted ms-2" style="font-size:13px;">— {{ $divisions->total() }} divisi terdaftar</span>
    </div>
    <a href="{{ route('divisions.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Divisi
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($divisions->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-building d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
                <p>Belum ada divisi terdaftar.</p>
                <a href="{{ route('divisions.create') }}" class="btn btn-sm btn-primary mt-1">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Divisi Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:90px;">Kode</th>
                            <th>Nama Divisi</th>
                            <th>Kepala Divisi</th>
                            <th>Email Approver</th>
                            <th style="width:80px; text-align:center;">Dokumen</th>
                            <th style="width:80px; text-align:center;">Status</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $div)
                        <tr class="{{ !$div->is_active ? 'table-secondary' : '' }}">
                            <td>
                                <span class="badge bg-dark font-monospace px-2">{{ $div->code }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                                    {{ $div->name }}
                                </div>
                                @if($div->head_phone)
                                    <small class="text-muted">
                                        <i class="bi bi-telephone me-1"></i>{{ $div->head_phone }}
                                    </small>
                                @endif
                            </td>
                            <td style="font-size:13px;">{{ $div->head_name }}</td>
                            <td>
                                <span style="font-size:12px;" class="text-muted">
                                    <i class="bi bi-envelope me-1"></i>{{ $div->head_email }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $div->documents_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($div->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Detail --}}
                                    <a href="{{ route('divisions.show', $div) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('divisions.edit', $div) }}"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    {{-- Toggle aktif --}}
                                    <form method="POST"
                                          action="{{ route('divisions.toggle', $div) }}"
                                          onsubmit="return confirm('{{ $div->is_active ? 'Nonaktifkan' : 'Aktifkan' }} divisi ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm {{ $div->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $div->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi {{ $div->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                    {{-- Hapus --}}
                                    @if($div->documents_count === 0)
                                        <form method="POST"
                                              action="{{ route('divisions.destroy', $div) }}"
                                              onsubmit="return confirm('Hapus divisi {{ $div->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($divisions->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <small class="text-muted">
                        Menampilkan {{ $divisions->firstItem() }}–{{ $divisions->lastItem() }}
                        dari {{ $divisions->total() }} divisi
                    </small>
                    {{ $divisions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * { padding:12px 14px; }
    .page-link  { color:var(--esdm-navy); border-color:#d4c9a8; font-size:13px; }
    .page-item.active .page-link { background:var(--esdm-navy); border-color:var(--esdm-navy); }
</style>
@endpush
