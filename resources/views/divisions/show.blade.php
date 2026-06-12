@extends('layouts.app')
@section('title', 'Detail Divisi')
@section('page-title', 'Detail Divisi')

@section('content')
<div class="gold-line"></div>

<div class="row g-4">

    {{-- ── KIRI: Info Divisi ── --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i> Info Divisi
                </span>
                @if($division->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Nonaktif</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row div-info">
                    <dt class="col-sm-4">Kode</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-dark font-monospace fs-6">{{ $division->code }}</span>
                    </dd>
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $division->name }}</dd>
                    <dt class="col-sm-4">Total Dokumen</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-light text-dark border fs-6">
                            {{ $division->documents_count }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-badge"></i> Data Approver
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px; height:48px; border-radius:50%; background:var(--esdm-gold); display:flex; align-items:center; justify-content:center; font-size:1.3rem; font-weight:700; color:var(--esdm-navy); flex-shrink:0;">
                        {{ strtoupper(substr($division->head_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:14px; color:var(--esdm-navy);">
                            {{ $division->head_name }}
                        </div>
                        <div class="text-muted" style="font-size:12px;">Kepala Divisi</div>
                    </div>
                </div>

                <dl class="row div-info mb-0">
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:{{ $division->head_email }}" style="font-size:13px;">
                            {{ $division->head_email }}
                        </a>
                    </dd>
                    @if($division->head_phone)
                    <dt class="col-sm-4">WhatsApp</dt>
                    <dd class="col-sm-8" style="font-size:13px;">{{ $division->head_phone }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- ── KANAN: Dokumen Terbaru ── --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-folder2-open"></i> 5 Dokumen Terbaru
                </span>
                <a href="{{ route('documents.index', ['division_id' => $division->id]) }}"
                   class="btn btn-sm btn-outline-secondary" style="font-size:12px;">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentDocuments->isEmpty())
                    <div class="text-center py-4 text-muted" style="font-size:13px;">
                        <i class="bi bi-folder2 d-block mb-1" style="font-size:1.5rem; color:#d4c9a8;"></i>
                        Belum ada dokumen di divisi ini.
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentDocuments as $doc)
                        <li class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                                        {{ Str::limit($doc->title, 55) }}
                                    </div>
                                    <small class="text-muted font-monospace">{{ $doc->document_number }}</small>
                                    <small class="text-muted ms-2">
                                        {{ $doc->document_date->translatedFormat('d M Y') }}
                                    </small>
                                </div>
                                <span class="badge badge-{{ str_replace('_','-',$doc->classification) }} flex-shrink-0">
                                    {{ $doc->classification_label }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="mt-4 d-flex gap-2">
    <a href="{{ route('divisions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <a href="{{ route('divisions.edit', $division) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i> Edit Divisi
    </a>
</div>
@endsection

@push('styles')
<style>
    .div-info dt { font-weight:600; font-size:13px; color:#6b7280; }
    .div-info dd { font-size:13px; color:var(--esdm-navy); margin-bottom:10px; }
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }
</style>
@endpush
