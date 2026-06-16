@extends('layouts.app')

@section('title', 'Status Approval Upload')
@section('page-title', 'Status Persetujuan Upload Dokumen')

@section('content')
<div class="gold-line"></div>

@php
    $step1 = $uploadApproval->steps->firstWhere('step_order', 1);
    $step2 = $uploadApproval->steps->firstWhere('step_order', 2);

    $overallStatus = $uploadApproval->status; // pending, approved, rejected

    $statusConfig = match($overallStatus) {
        'approved' => ['label' => 'Disetujui & Aktif', 'color' => 'success', 'icon' => 'bi-check-circle-fill'],
        'rejected' => ['label' => 'Ditolak',           'color' => 'danger',  'icon' => 'bi-x-circle-fill'],
        default    => ['label' => 'Menunggu Persetujuan', 'color' => 'warning', 'icon' => 'bi-hourglass-split'],
    };
@endphp

<div class="row justify-content-center">
<div class="col-lg-8">

{{-- ── INFO DOKUMEN ── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-file-earmark-lock2"></i>
        Informasi Dokumen
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Perihal / Judul</div>
                <div class="fw-semibold" style="color:var(--esdm-navy); font-size:15px;">{{ $document->title }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Status Saat Ini</div>
                <span class="badge bg-{{ $statusConfig['color'] }} px-3 py-2 mt-1" style="font-size:12px;">
                    <i class="bi {{ $statusConfig['icon'] }} me-1"></i>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
            <div class="col-md-4">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Nomor Surat</div>
                <div class="font-monospace" style="font-size:13px;">{{ $document->document_number }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Tanggal Dokumen</div>
                <div style="font-size:13px;">{{ $document->document_date->translatedFormat('d F Y') }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Divisi</div>
                <div style="font-size:13px;">{{ $document->division->name ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Diupload Oleh</div>
                <div style="font-size:13px;">{{ $document->uploader->name ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Waktu Upload</div>
                <div style="font-size:13px;">{{ $uploadApproval->requested_at->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>
    </div>
</div>

{{-- ── TIMELINE APPROVAL ── --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3"></i>
        Progress Persetujuan
    </div>
    <div class="card-body p-0">

        {{-- Step 1 --}}
        <div class="approval-step {{ $step1?->status }}">
            @include('documents._approval_step_row', ['step' => $step1, 'stepNum' => 1])
        </div>

        {{-- Connector --}}
        <div class="step-connector"></div>

        {{-- Step 2 --}}
        <div class="approval-step {{ $step2?->status }}">
            @include('documents._approval_step_row', ['step' => $step2, 'stepNum' => 2])
        </div>

    </div>

    @if($uploadApproval->completed_at)
    <div class="card-footer text-muted" style="font-size:12px;">
        <i class="bi bi-clock-history me-1"></i>
        Proses approval selesai pada
        {{ $uploadApproval->completed_at->translatedFormat('d F Y, H:i') }} WIB
    </div>
    @endif
</div>

{{-- Tombol kembali --}}
<div class="mt-3">
    <a href="{{ route('documents.index', ['status' => 'pending']) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
    .approval-step {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    .approval-step:last-child { border-bottom: none; }

    .step-connector {
        height: 1px;
        background: #e5e7eb;
        margin: 0 24px;
    }

    .step-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .step-badge.waiting  { background: #f3f4f6; color: #9ca3af; border: 2px solid #e5e7eb; }
    .step-badge.sent     { background: #eff6ff; color: #3b82f6; border: 2px solid #bfdbfe; }
    .step-badge.approved { background: #d1fae5; color: #059669; border: 2px solid #a7f3d0; }
    .step-badge.rejected { background: #fee2e2; color: #dc2626; border: 2px solid #fca5a5; }

    .step-status-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 12px;
        letter-spacing: .3px;
    }
    .pill-waiting  { background: #f3f4f6; color: #6b7280; }
    .pill-sent     { background: #eff6ff; color: #1d4ed8; }
    .pill-approved { background: #d1fae5; color: #065f46; }
    .pill-rejected { background: #fee2e2; color: #7f1d1d; }

    .rejection-box {
        background: #fff7ed;
        border-left: 3px solid #f97316;
        padding: 10px 14px;
        border-radius: 0 6px 6px 0;
        font-size: 12px;
        color: #9a3412;
        margin-top: 10px;
    }
</style>
@endpush
