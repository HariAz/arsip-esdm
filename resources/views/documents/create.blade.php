@extends('layouts.app')

@section('title', 'Upload Dokumen')
@section('page-title', 'Upload Dokumen Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Arsip Dokumen</a></li>
    <li class="breadcrumb-item active">Upload Dokumen</li>
@endsection

@section('content')
<div class="gold-line"></div>

<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-cloud-upload"></i>
        Form Upload Dokumen
    </div>
    <div class="card-body p-4">

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="upload-form">
            @csrf

            {{-- ── BARIS 1: Nomor Surat & Tanggal ── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label required-label">Nomor Surat</label>
                    <input
                        type="text"
                        name="document_number"
                        class="form-control @error('document_number') is-invalid @enderror"
                        value="{{ old('document_number') }}"
                        placeholder="Contoh: B-001/ESDM/2024"
                    >
                    @error('document_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label required-label">Tanggal Dokumen</label>
                    <input
                        type="date"
                        name="document_date"
                        class="form-control @error('document_date') is-invalid @enderror"
                        value="{{ old('document_date', date('Y-m-d')) }}"
                    >
                    @error('document_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── BARIS 2: Perihal ── --}}
            <div class="mb-3">
                <label class="form-label required-label">Perihal / Judul Dokumen</label>
                <input
                    type="text"
                    name="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title') }}"
                    placeholder="Contoh: Undangan Rapat Koordinasi Triwulan I"
                >
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── BARIS 3: Divisi & Jenis ── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label required-label">Divisi</label>
                    <select name="division_id" class="form-select @error('division_id') is-invalid @enderror">
                        <option value="">— Pilih Divisi —</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Dokumen <span class="text-muted fw-normal">(opsional)</span></label>
                    <input
                        type="text"
                        name="document_type"
                        class="form-control @error('document_type') is-invalid @enderror"
                        value="{{ old('document_type') }}"
                        placeholder="Contoh: SK, Laporan, Undangan, MoU..."
                        list="doc-type-suggestions"
                    >
                    <datalist id="doc-type-suggestions">
                        <option value="Surat Keputusan (SK)">
                        <option value="Laporan Hasil Uji">
                        <option value="Undangan">
                        <option value="Nota Dinas">
                        <option value="MoU / Perjanjian Kerja Sama">
                        <option value="Surat Edaran">
                        <option value="Berita Acara">
                        <option value="Laporan Bulanan">
                        <option value="Laporan Triwulan">
                        <option value="Dokumen Sewa Gedung">
                    </datalist>
                    @error('document_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── BARIS 4: Kode Klasifikasi Dokumen ── --}}
            <div class="mb-3">
                <label class="form-label required-label">Kode Klasifikasi Dokumen</label>
                @include('documents._classification_code_select', [
                    'selectedCode' => old('document_classification_code', '')
                ])
                @error('document_classification_code')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted" style="font-size:11px;">
                    Berdasarkan Kepmen ESDM No. 167 K/04/MEM/2020.
                    Berbeda dari klasifikasi keamanan di bawah.
                </small>
            </div>

            {{-- ── BARIS 5: Klasifikasi ── --}}
            <div class="mb-3">
                <label class="form-label required-label">Klasifikasi Keamanan</label>
                <div class="row g-2" id="classification-options">

                    @php
                        $classOptions = [
                            ['value' => 'biasa',          'label' => 'Biasa / Terbuka',  'desc' => 'Dapat diakses dan didownload langsung.', 'icon' => 'bi-unlock',        'color' => 'success'],
                            ['value' => 'terbatas',       'label' => 'Terbatas',          'desc' => 'Perlu persetujuan untuk download.',       'icon' => 'bi-lock',          'color' => 'info'],
                            ['value' => 'rahasia',        'label' => 'Rahasia',           'desc' => 'Preview tidak tersedia. Perlu approval.',  'icon' => 'bi-shield-lock',   'color' => 'warning'],
                            ['value' => 'sangat_rahasia', 'label' => 'Sangat Rahasia',    'desc' => 'Upload & download perlu approval penuh.',  'icon' => 'bi-shield-fill-x', 'color' => 'danger'],
                        ];
                    @endphp

                    @foreach($classOptions as $opt)
                    <div class="col-md-3 col-6">
                        <input
                            type="radio"
                            class="btn-check"
                            name="classification"
                            id="cls_{{ $opt['value'] }}"
                            value="{{ $opt['value'] }}"
                            {{ old('classification', 'biasa') === $opt['value'] ? 'checked' : '' }}
                        >
                        <label class="cls-card w-100 h-100" for="cls_{{ $opt['value'] }}" data-color="{{ $opt['color'] }}">
                            <i class="bi {{ $opt['icon'] }} cls-icon text-{{ $opt['color'] }}"></i>
                            <div class="cls-label">{{ $opt['label'] }}</div>
                            <div class="cls-desc">{{ $opt['desc'] }}</div>
                        </label>
                    </div>
                    @endforeach

                </div>
                @error('classification')
                    <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── BARIS 5: Upload File ── --}}
            <div class="mb-4">
                <label class="form-label required-label">File PDF</label>
                <div
                    id="drop-zone"
                    class="drop-zone @error('file') border-danger @enderror"
                    onclick="document.getElementById('file-input').click()"
                >
                    <div id="drop-zone-content">
                        <i class="bi bi-file-earmark-pdf drop-icon"></i>
                        <div class="drop-text">Klik atau seret file PDF ke sini</div>
                        <div class="drop-sub">Format: PDF · Maks. 20 MB</div>
                    </div>
                    <div id="file-preview" class="d-none">
                        <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size:2rem;"></i>
                        <div id="file-name" class="fw-semibold mt-1" style="font-size:13px;"></div>
                        <div id="file-size" class="text-muted" style="font-size:12px;"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearFile(event)">
                            <i class="bi bi-x me-1"></i>Ganti File
                        </button>
                    </div>
                </div>
                <input type="file" id="file-input" name="file" accept=".pdf" class="d-none">
                @error('file')
                    <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── PERINGATAN SANGAT RAHASIA ── --}}
            <div id="warning-sangat-rahasia" class="alert alert-warning d-flex gap-2 d-none mb-3">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Perhatian — Dokumen Sangat Rahasia</strong><br>
                    <small>Dokumen ini tidak akan langsung aktif. Sistem akan mengirim magic link ke Bagian Umum dan Kepala Divisi untuk persetujuan sebelum dokumen masuk arsip.</small>
                </div>
            </div>

            {{-- ── TOMBOL ── --}}
            <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="bi bi-cloud-upload me-1"></i>
                    <span id="btn-text">Upload Dokumen</span>
                </button>
            </div>

        </form>
    </div>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
    .required-label::after {
        content: ' *';
        color: #dc2626;
    }

    /* Klasifikasi card */
    .cls-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 14px 10px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all .15s;
        background: #fff;
    }
    .cls-card:hover { border-color: #9ca3af; }
    .btn-check:checked + .cls-card {
        border-color: var(--esdm-navy);
        background: #f0f4f8;
        box-shadow: 0 0 0 3px rgba(13,43,78,.12);
    }
    .cls-icon { font-size: 1.6rem; margin-bottom: 6px; }
    .cls-label { font-size: 12px; font-weight: 700; color: var(--esdm-navy); }
    .cls-desc  { font-size: 10px; color: #6b7280; margin-top: 3px; line-height: 1.3; }

    /* Drop zone */
    .drop-zone {
        border: 2px dashed #d4c9a8;
        border-radius: 8px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #faf8f3;
    }
    .drop-zone:hover, .drop-zone.drag-over {
        border-color: var(--esdm-gold);
        background: #fffdf5;
    }
    .drop-icon { font-size: 2.5rem; color: #d4c9a8; display: block; margin-bottom: 8px; }
    .drop-text { font-size: 14px; font-weight: 600; color: var(--esdm-navy); }
    .drop-sub  { font-size: 12px; color: #9ca3af; margin-top: 4px; }
</style>
@endpush

@push('scripts')
<script>
// ── File input handler ──
const fileInput = document.getElementById('file-input');
const dropZone  = document.getElementById('drop-zone');

fileInput.addEventListener('change', () => showFile(fileInput.files[0]));

// Drag & Drop
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        fileInput.files = e.dataTransfer.files;
        showFile(file);
    } else {
        alert('Hanya file PDF yang diperbolehkan.');
    }
});

function showFile(file) {
    if (!file) return;
    document.getElementById('drop-zone-content').classList.add('d-none');
    document.getElementById('file-preview').classList.remove('d-none');
    document.getElementById('file-name').textContent = file.name;
    const mb = (file.size / (1024 * 1024)).toFixed(2);
    const kb = (file.size / 1024).toFixed(1);
    document.getElementById('file-size').textContent = file.size > 1024*1024 ? mb + ' MB' : kb + ' KB';
}

function clearFile(e) {
    e.stopPropagation();
    fileInput.value = '';
    document.getElementById('drop-zone-content').classList.remove('d-none');
    document.getElementById('file-preview').classList.add('d-none');
}

// ── Peringatan sangat rahasia ──
document.querySelectorAll('input[name="classification"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const warn = document.getElementById('warning-sangat-rahasia');
        warn.classList.toggle('d-none', radio.value !== 'sangat_rahasia' || !radio.checked);
    });
});

// ── Loading state saat submit ──
document.getElementById('upload-form').addEventListener('submit', function () {
    const btn  = document.getElementById('btn-submit');
    const text = document.getElementById('btn-text');
    btn.disabled = true;
    text.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengunggah...';
});
</script>
@endpush
