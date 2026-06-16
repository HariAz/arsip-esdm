@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="gold-line"></div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <span class="fw-semibold" style="font-size:14px; color:var(--esdm-navy);">
        <i class="bi bi-people me-1"></i> Daftar Pengguna
        <span class="text-muted fw-normal ms-1" style="font-size:13px;">— {{ $users->total() }} pengguna</span>
    </span>
    <a href="{{ route('users.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
                Belum ada pengguna terdaftar.
            </div>
        @else
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Login Terakhir</th>
                            <th>Terdaftar</th>
                            <th style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--esdm-gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--esdm-navy);flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold" style="font-size:13px;">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-light text-muted border" style="font-size:10px;">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td style="font-size:13px;">{{ $user->email }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:12px;">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '—' }}
                            </td>
                            <td class="text-muted" style="font-size:12px;">
                                {{ $user->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:160px;font-size:13px;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                                <i class="bi bi-pencil me-2 text-info"></i>Edit
                                            </a>
                                        </li>
                                        @if($user->id !== auth()->id())
                                        <li>
                                            <form action="{{ route('users.toggle', $user) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item {{ $user->is_active ? 'text-warning' : 'text-success' }}">
                                                    @if($user->is_active)
                                                        <i class="bi bi-toggle-off me-2"></i>Nonaktifkan
                                                    @else
                                                        <i class="bi bi-toggle-on me-2"></i>Aktifkan
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <small class="text-muted">
                        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                        dari {{ $users->total() }} pengguna
                    </small>
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * { padding: 12px 14px; }
    .pagination { margin: 0; }
    .page-link  { color: var(--esdm-navy); border-color: #d4c9a8; font-size:13px; }
    .page-item.active .page-link { background: var(--esdm-navy); border-color: var(--esdm-navy); }
</style>
@endpush
