{{-- resources/views/divisions/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Divisi')
@section('page-title', 'Tambah Divisi Baru')

@section('content')
<div class="gold-line"></div>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-building-add"></i> Form Tambah Divisi
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('divisions.store') }}">
            @csrf
            @include('divisions._form', ['division' => null])
            <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                <a href="{{ route('divisions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Simpan Divisi
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
