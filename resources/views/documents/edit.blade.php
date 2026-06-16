@extends('layouts.app')

@section('title', 'Edit Dokumen')
@section('page-title', 'Edit Metadata Dokumen')

@section('content')
<div class="gold-line"></div>

<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square"></i>
        Edit Metadata Dokumen
    </div>
    <div class="card-body p-4">

        {{-- Info dokumen yang sedang diedit --}}
        <div class="alert alert-secondary d-flex gap-2 py-2 mb-4" style="font-size:12px;">
            <i class="bi bi-file-earmark-text flex-shrink-0 mt-1"></i>
            <div>
                <strong>{{ $document->document_number }}</strong> &mdash; {{ $document->file_name }}
                <br>
                <span class="text-muted">Klasifikasi keamanan dan file PDF tidak dapat diubah setelah upload.</span>
            </div>
        </div>

        <form method="POST" action="{{ route('documents.update', $document) }}">
            @csrf
            @method('PATCH')

            {{-- ── BARIS 1: Nomor Surat (read-only) & Tanggal ── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Nomor Surat</label>
                    <input
                        type="text"
                        class="form-control bg-light"
                        value="{{ $document->document_number }}"
                        disabled
                    >
                    <small class="text-muted" style="font-size:11px;">Nomor surat tidak dapat diubah.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label required-label">Tanggal Dokumen</label>
                    <input
                        type="date"
                        name="document_date"
                        class="form-control @error('document_date') is-invalid @enderror"
                        value="{{ old('document_date', $document->document_date->format('Y-m-d')) }}"
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
                    value="{{ old('title', $document->title) }}"
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
                            <option value="{{ $div->id }}" {{ old('division_id', $document->division_id) == $div->id ? 'selected' : '' }}>
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
                        value="{{ old('document_type', $document->document_type) }}"
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
                    'selectedCode' => old('document_classification_code', $document->document_classification_code)
                ])
                @error('document_classification_code')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted" style="font-size:11px;">
                    Berdasarkan Kepmen ESDM No. 167 K/04/MEM/2020.
                </small>
            </div>

            {{-- ── BARIS 5: Klasifikasi Keamanan (read-only) ── --}}
            <div class="mb-4">
                <label class="form-label">Klasifikasi Keamanan</label>
                <div class="d-flex align-items-center gap-2 p-3 rounded border bg-light">
                    <span class="badge bg-{{ $document->classification_color }} fs-6">
                        {{ $document->classification_label }}
                    </span>
                    <small class="text-muted">Tidak dapat diubah setelah dokumen diupload.</small>
                </div>
            </div>

            {{-- ── TOMBOL ── --}}
            <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
</style>
@endpush
