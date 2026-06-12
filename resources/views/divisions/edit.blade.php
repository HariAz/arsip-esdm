@extends('layouts.app')
@section('title', 'Edit Divisi')
@section('page-title', 'Edit Divisi')

@section('content')
<div class="gold-line"></div>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square"></i>
        Edit Divisi — <span class="badge bg-dark font-monospace ms-1">{{ $division->code }}</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('divisions.update', $division) }}">
            @csrf
            @method('PUT')
            @include('divisions._form', ['division' => $division])

            {{-- Toggle aktif --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                    Status Divisi
                </label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           name="is_active" id="is_active" value="1"
                           {{ $division->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active" style="font-size:13px;">
                        Divisi Aktif
                    </label>
                </div>
                <small class="text-muted" style="font-size:11px;">
                    Divisi nonaktif tidak akan muncul di pilihan saat upload dokumen.
                </small>
            </div>

            <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                <a href="{{ route('divisions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
