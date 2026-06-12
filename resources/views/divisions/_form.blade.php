{{-- resources/views/divisions/_form.blade.php --}}
{{-- Dipakai oleh create.blade.php dan edit.blade.php --}}

{{-- Kode Divisi --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label required-label">Kode Divisi</label>
        <input
            type="text"
            name="code"
            class="form-control font-monospace @error('code') is-invalid @enderror"
            value="{{ old('code', $division?->code) }}"
            placeholder="Contoh: DIV-01"
            style="text-transform:uppercase;"
            oninput="this.value = this.value.toUpperCase()"
        >
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted" style="font-size:11px;">Huruf kapital, angka, dan tanda hubung saja.</small>
    </div>

    <div class="col-md-8">
        <label class="form-label required-label">Nama Divisi</label>
        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $division?->name) }}"
            placeholder="Contoh: Divisi Perencanaan dan Keuangan"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Separator: Bagian Umum --}}
<div class="mb-3 mt-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="height:1px; flex:1; background:#e5e7eb;"></div>
        <span style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">
            Bagian Umum (Approver Step 1)
        </span>
        <div style="height:1px; flex:1; background:#e5e7eb;"></div>
    </div>

    <div class="alert alert-info d-flex gap-2 py-2" style="font-size:12px;">
        <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
        <span>
            Magic link <strong>persetujuan pertama</strong> akan dikirim ke email Bagian Umum sebelum diteruskan ke Kepala Divisi.
        </span>
    </div>
</div>

{{-- Nama Bagian Umum --}}
<div class="mb-3">
    <label class="form-label required-label">Nama Bagian Umum</label>
    <input
        type="text"
        name="general_affairs_name"
        class="form-control @error('general_affairs_name') is-invalid @enderror"
        value="{{ old('general_affairs_name', $division?->general_affairs_name) }}"
        placeholder="Nama petugas / staf Bagian Umum divisi"
    >
    @error('general_affairs_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Email Bagian Umum --}}
<div class="mb-3">
    <label class="form-label required-label">Email Bagian Umum</label>
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-envelope text-muted"></i>
        </span>
        <input
            type="email"
            name="general_affairs_email"
            class="form-control border-start-0 @error('general_affairs_email') is-invalid @enderror"
            value="{{ old('general_affairs_email', $division?->general_affairs_email) }}"
            placeholder="bagianumum@esdm.go.id"
        >
        @error('general_affairs_email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <small class="text-muted" style="font-size:11px;">
        Magic link Step 1 akan dikirim ke email ini.
    </small>
</div>

{{-- Separator: Kepala Divisi --}}
<div class="mb-3 mt-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="height:1px; flex:1; background:#e5e7eb;"></div>
        <span style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">
            Kepala Divisi (Approver Step 2)
        </span>
        <div style="height:1px; flex:1; background:#e5e7eb;"></div>
    </div>
</div>

{{-- Nama Kepala --}}
<div class="mb-3">
    <label class="form-label required-label">Nama Kepala Divisi</label>
    <input
        type="text"
        name="head_name"
        class="form-control @error('head_name') is-invalid @enderror"
        value="{{ old('head_name', $division?->head_name) }}"
        placeholder="Nama lengkap kepala divisi"
    >
    @error('head_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Email & Telepon --}}
<div class="row g-3 mb-3">
    <div class="col-md-7">
        <label class="form-label required-label">Email Kepala Divisi</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-envelope text-muted"></i>
            </span>
            <input
                type="email"
                name="head_email"
                class="form-control border-start-0 @error('head_email') is-invalid @enderror"
                value="{{ old('head_email', $division?->head_email) }}"
                placeholder="email@esdm.go.id"
            >
            @error('head_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted" style="font-size:11px;">
            Magic link Step 2 akan dikirim ke email ini.
        </small>
    </div>
    <div class="col-md-5">
        <label class="form-label">
            Nomor WhatsApp
            <span class="text-muted fw-normal">(opsional)</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-whatsapp text-success"></i>
            </span>
            <input
                type="text"
                name="head_phone"
                class="form-control border-start-0 @error('head_phone') is-invalid @enderror"
                value="{{ old('head_phone', $division?->head_phone) }}"
                placeholder="08xxxxxxxxxx"
            >
            @error('head_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

@push('styles')
<style>
    .required-label::after { content:' *'; color:#dc2626; }
    .form-label { font-size:13px; font-weight:600; color:var(--esdm-navy); margin-bottom:6px; }
</style>
@endpush
