@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas Sistem')

@section('content')
<div class="gold-line"></div>

{{-- ── PANEL FILTER ── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel"></i> Filter Log
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('activity-logs.index') }}">
            <div class="row g-3">

                {{-- Kata kunci --}}
                <div class="col-md-3">
                    <label class="filter-label">Kata Kunci</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Deskripsi atau nama aktor..."
                        value="{{ request('search') }}">
                </div>

                {{-- Jenis Aksi --}}
                <div class="col-md-2">
                    <label class="filter-label">Jenis Aksi</label>
                    <select name="action" class="form-select">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            @php
                                $parts = explode('.', $act);
                                $label = match($act) {
                                    'user.login'              => '🔑 Login',
                                    'user.logout'             => '🚪 Logout',
                                    'document.upload'         => '📤 Upload Dokumen',
                                    'document.view'           => '👁️ Lihat Dokumen',
                                    'document.search'         => '🔍 Pencarian',
                                    'download.requested'      => '📋 Request Download',
                                    'download.magic_sent'     => '📧 Magic Link Kirim',
                                    'download.approved_step'  => '✅ Approval Download',
                                    'download.rejected_step'  => '❌ Tolak Download',
                                    'download.completed'      => '🎉 Download Disetujui',
                                    'download.executed'       => '⬇️ File Didownload',
                                    'upload.approval_sent'    => '📧 Approval Upload Kirim',
                                    'upload.approved'         => '✅ Upload Disetujui',
                                    'upload.rejected'         => '❌ Upload Ditolak',
                                    'division.created'        => '🏢 Divisi Dibuat',
                                    'division.updated'        => '✏️ Divisi Diubah',
                                    'division.toggled'        => '🔄 Status Divisi',
                                    'division.deleted'        => '🗑️ Divisi Dihapus',
                                    default                   => ucwords(str_replace(['.','_'], ' ', $act)),
                                };
                            @endphp
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- User --}}
                <div class="col-md-2">
                    <label class="filter-label">Pengguna</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                        <option value="external" {{ request('user_id') === 'external' ? 'selected' : '' }}>
                            — Approver Eksternal —
                        </option>
                    </select>
                </div>

                {{-- Tanggal dari --}}
                <div class="col-md-2">
                    <label class="filter-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control"
                        value="{{ request('date_from') }}">
                </div>

                {{-- Tanggal sampai --}}
                <div class="col-md-2">
                    <label class="filter-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control"
                        value="{{ request('date_to') }}">
                </div>

                {{-- Tombol --}}
                <div class="col-md-1 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary w-100" title="Filter">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->hasAny(['search','action','user_id','date_from','date_to']))
                        <a href="{{ route('activity-logs.index') }}"
                           class="btn btn-outline-secondary w-100" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ── HEADER HASIL ── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <span class="fw-semibold" style="font-size:14px; color:var(--esdm-navy);">
        <i class="bi bi-clock-history me-1"></i>
        Riwayat Aktivitas
    </span>
    <span class="text-muted" style="font-size:13px;">
        {{ $logs->total() }} catatan ditemukan
    </span>
</div>

{{-- ── TABEL LOG ── --}}
<div class="card">
    <div class="card-body p-0">
        @if($logs->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history d-block mb-2"
                   style="font-size:2.5rem; color:#d4c9a8;"></i>
                <p>Tidak ada aktivitas ditemukan.</p>
                @if(request()->hasAny(['search','action','user_id','date_from','date_to']))
                    <a href="{{ route('activity-logs.index') }}"
                       class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:160px;">Waktu</th>
                            <th style="width:120px;">Aksi</th>
                            <th>Deskripsi</th>
                            <th style="width:140px;">Pelaku</th>
                            <th style="width:120px;">Dokumen</th>
                            <th style="width:110px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            {{-- Waktu --}}
                            <td>
                                <div style="font-size:12px; color:var(--esdm-navy); font-weight:600;">
                                    {{ $log->created_at->translatedFormat('d M Y') }}
                                </div>
                                <div style="font-size:11px; color:#9ca3af;">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>

                            {{-- Badge Aksi --}}
                            <td>
                                <span class="action-badge action-{{ Str::before($log->action, '.') }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td style="font-size:13px;">
                                {{ $log->description ?? '—' }}
                                @if($log->metadata)
                                    <button class="btn btn-link btn-sm p-0 ms-1"
                                            style="font-size:11px; color:#9ca3af;"
                                            onclick="toggleMeta({{ $log->id }})">
                                        [meta]
                                    </button>
                                    <div id="meta-{{ $log->id }}" class="d-none mt-1">
                                        <code style="font-size:10px; background:#f3f4f6;
                                               padding:4px 8px; border-radius:4px;
                                               display:block; white-space:pre-wrap;">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                                    </div>
                                @endif
                            </td>

                            {{-- Pelaku --}}
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="mini-avatar">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:12px; font-weight:600;
                                                        color:var(--esdm-navy);">
                                                {{ Str::limit($log->user->name, 18) }}
                                            </div>
                                            <div style="font-size:10px; color:#9ca3af;">
                                                Arsiparis
                                            </div>
                                        </div>
                                    </div>
                                @elseif($log->actor_name)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="mini-avatar" style="background:#f0fdf4;
                                                    color:#166534; border:1px solid #bbf7d0;">
                                            {{ strtoupper(substr($log->actor_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:12px; font-weight:600;
                                                        color:#166534;">
                                                {{ Str::limit($log->actor_name, 18) }}
                                            </div>
                                            <div style="font-size:10px; color:#9ca3af;">
                                                Approver
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size:12px;">Sistem</span>
                                @endif
                            </td>

                            {{-- Dokumen --}}
                            <td>
                                @if($log->document)
                                    <a href="{{ route('documents.show', $log->document) }}"
                                       style="font-size:12px; color:var(--esdm-navy);"
                                       title="{{ $log->document->title }}">
                                        {{ Str::limit($log->document->title, 25) }}
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            </td>

                            {{-- IP + User Agent --}}
                            <td>
                                @if($log->ip_address)
                                <span class="font-monospace text-muted" style="font-size:11px;"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="left"
                                      title="{{ $log->user_agent ?? 'User agent tidak tersedia' }}">
                                    {{ $log->ip_address }}
                                </span>
                                @if($log->user_agent)
                                <button class="btn btn-link p-0 ms-1 ua-btn"
                                        style="font-size:10px; color:#d4c9a8; vertical-align:middle;"
                                        onclick="toggleUA({{ $log->id }})">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <div id="ua-{{ $log->id }}" class="d-none mt-1"
                                     style="font-size:10px; color:#9ca3af; word-break:break-all; max-width:200px;">
                                    {{ $log->user_agent }}
                                </div>
                                @endif
                                @else
                                <span class="text-muted" style="font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="d-flex justify-content-between align-items-center
                            px-4 py-3 border-top">
                    <small class="text-muted">
                        Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }}
                        dari {{ $logs->total() }} catatan
                    </small>
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--esdm-navy);
        margin-bottom: 5px;
        display: block;
    }

    .table > :not(caption) > * > * { padding: 10px 14px; vertical-align: middle; }

    /* Badge aksi */
    .action-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        font-family: monospace;
        white-space: nowrap;
    }
    .action-user     { background: #eff6ff; color: #1e40af; }
    .action-document { background: #f0fdf4; color: #166534; }
    .action-download { background: #fef9c3; color: #92400e; }
    .action-upload   { background: #faf5ff; color: #6b21a8; }
    .action-division { background: #f0f4f8; color: #374151; }

    /* Mini avatar */
    .mini-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--esdm-gold);
        color: var(--esdm-navy);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* Pagination */
    .page-link { color: var(--esdm-navy); border-color: #d4c9a8; font-size: 13px; }
    .page-item.active .page-link {
        background: var(--esdm-navy);
        border-color: var(--esdm-navy);
    }
</style>
@endpush

@push('scripts')
<script>
function toggleMeta(id) {
    document.getElementById('meta-' + id).classList.toggle('d-none');
}
function toggleUA(id) {
    document.getElementById('ua-' + id).classList.toggle('d-none');
}
// Aktifkan Bootstrap tooltips
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
});
</script>
@endpush
