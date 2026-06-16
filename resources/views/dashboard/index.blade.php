@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="gold-line"></div>

{{-- ── STAT CARDS ROW 1 ── --}}
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #065f46;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-folder2-open" style="font-size:22px;color:#065f46;"></i>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#065f46;line-height:1;">{{ $stats['total_active'] }}</div>
                    <div style="font-size:12px;color:#6b7280;">Dokumen Aktif</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #c8972a;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-hourglass-split" style="font-size:22px;color:#92400e;"></i>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#92400e;line-height:1;">{{ $stats['total_pending'] }}</div>
                    <div style="font-size:12px;color:#6b7280;">Menunggu Approval</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #1e3a8a;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-download" style="font-size:22px;color:#1e3a8a;"></i>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#1e3a8a;line-height:1;">{{ $stats['download_pending'] }}</div>
                    <div style="font-size:12px;color:#6b7280;">Permintaan Download</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #0d2b4e;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:#e0e7ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-people" style="font-size:22px;color:#0d2b4e;"></i>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#0d2b4e;line-height:1;">{{ $stats['total_users'] }}</div>
                    <div style="font-size:12px;color:#6b7280;">Total Pengguna</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 2: Klasifikasi + Download Status ── --}}
<div class="row g-3 mb-4">

    {{-- Distribusi klasifikasi --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart"></i> Distribusi Dokumen per Klasifikasi
            </div>
            <div class="card-body">
                @php $total = max($stats['total_active'], 1); @endphp

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span><span class="badge" style="background:#d1fae5;color:#065f46;">Biasa</span></span>
                        <span class="fw-semibold">{{ $stats['biasa'] }} dokumen</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ round($stats['biasa']/$total*100) }}%;background:#065f46;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span><span class="badge" style="background:#dbeafe;color:#1e3a8a;">Terbatas</span></span>
                        <span class="fw-semibold">{{ $stats['terbatas'] }} dokumen</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ round($stats['terbatas']/$total*100) }}%;background:#1e3a8a;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span><span class="badge" style="background:#fef3c7;color:#92400e;">Rahasia</span></span>
                        <span class="fw-semibold">{{ $stats['rahasia'] }} dokumen</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ round($stats['rahasia']/$total*100) }}%;background:#92400e;"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span><span class="badge" style="background:#fee2e2;color:#7f1d1d;">Sangat Rahasia</span></span>
                        <span class="fw-semibold">{{ $stats['sangat_rahasia'] }} dokumen</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" style="width:{{ round($stats['sangat_rahasia']/$total*100) }}%;background:#7f1d1d;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status ringkasan --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-clipboard-data"></i> Ringkasan Status
            </div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:13px;">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3">Dokumen Aktif</td>
                            <td class="text-end pe-3"><span class="badge bg-success">{{ $stats['total_active'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Menunggu Approval Upload</td>
                            <td class="text-end pe-3"><span class="badge bg-warning text-dark">{{ $stats['total_pending'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Upload Ditolak</td>
                            <td class="text-end pe-3"><span class="badge bg-danger">{{ $stats['total_rejected'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Dihapus (Trash)</td>
                            <td class="text-end pe-3"><span class="badge bg-secondary">{{ $stats['total_deleted'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Permintaan Download Pending</td>
                            <td class="text-end pe-3"><span class="badge bg-warning text-dark">{{ $stats['download_pending'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Download Disetujui</td>
                            <td class="text-end pe-3"><span class="badge bg-info text-dark">{{ $stats['download_approved'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Download Selesai</td>
                            <td class="text-end pe-3"><span class="badge bg-success">{{ $stats['download_downloaded'] }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 3: Dokumen terbaru + Log aktivitas ── --}}
<div class="row g-3">

    {{-- Dokumen terbaru --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> Dokumen Terbaru
                </span>
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px;">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($recentDocs as $doc)
                <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom">
                    <i class="bi bi-file-earmark-pdf-fill text-danger mt-1" style="flex-shrink:0;"></i>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-semibold text-truncate" style="font-size:12px;color:var(--esdm-navy);">
                            {{ $doc->title }}
                        </div>
                        <div class="text-muted" style="font-size:11px;">
                            {{ $doc->division->name ?? '-' }} · {{ $doc->document_date->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <span class="badge badge-{{ str_replace('_','-',$doc->classification) }} flex-shrink-0" style="font-size:10px;">
                        {{ $doc->classification_label }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    Belum ada dokumen.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Log aktivitas terbaru --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-activity"></i> Aktivitas Terbaru
                </span>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px;">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @forelse($recentLogs as $log)
                <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom">
                    <div style="width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;">
                        @php
                            $icon = match(true) {
                                str_starts_with($log->action, 'user.')     => '👤',
                                str_starts_with($log->action, 'document.') => '📄',
                                str_starts_with($log->action, 'download.') => '⬇️',
                                str_starts_with($log->action, 'upload.')   => '⬆️',
                                str_starts_with($log->action, 'division.') => '🏢',
                                default                                    => '📋',
                            };
                        @endphp
                        {{ $icon }}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:12px;color:var(--esdm-navy);">{{ $log->description }}</div>
                        <div class="text-muted" style="font-size:11px;">
                            {{ $log->user?->name ?? $log->actor_name ?? 'Sistem' }}
                            · {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted" style="font-size:13px;">
                    Belum ada aktivitas.
                </div>
                @endforelse
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
    .min-width-0 { min-width: 0; }
</style>
@endpush
