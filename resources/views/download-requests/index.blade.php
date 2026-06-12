{{-- resources/views/download-requests/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Permintaan Download')
@section('page-title', 'Permintaan Download')

@section('content')
<div class="gold-line"></div>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span class="d-flex align-items-center gap-2">
      <i class="bi bi-download"></i> Riwayat Permintaan Download
    </span>
    <span class="text-muted" style="font-size:12px;">{{ $requests->total() }} permintaan</span>
  </div>
  <div class="card-body p-0">
    @if($requests->isEmpty())
      <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem; color:#d4c9a8;"></i>
        <p>Belum ada permintaan download.</p>
        <a href="{{ route('documents.index') }}" class="btn btn-sm btn-primary mt-1">
          <i class="bi bi-folder2-open me-1"></i>Lihat Arsip Dokumen
        </a>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Dokumen</th>
              <th>Klasifikasi</th>
              <th>Diajukan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requests as $i => $req)
            <tr>
              <td class="text-muted">{{ $requests->firstItem() + $i }}</td>
              <td>
                <div class="fw-semibold" style="font-size:13px; color:var(--esdm-navy);">
                  {{ Str::limit($req->document->title, 50) }}
                </div>
                <small class="text-muted font-monospace">{{ $req->document->document_number }}</small>
              </td>
              <td>
                <span class="badge badge-{{ str_replace('_','-',$req->document->classification) }} px-2">
                  {{ $req->document->classification_label }}
                </span>
              </td>
              <td style="font-size:12px;">{{ $req->requested_at->translatedFormat('d M Y, H:i') }}</td>
              <td>
                @if($req->status === 'pending')
                  <span class="badge bg-warning text-dark">Menunggu</span>
                @elseif($req->status === 'approved')
                  <span class="badge bg-success">Disetujui</span>
                @elseif($req->status === 'rejected')
                  <span class="badge bg-danger">Ditolak</span>
                @else
                  <span class="badge bg-secondary">Selesai</span>
                @endif
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('download-requests.show', $req) }}"
                     class="btn btn-sm btn-outline-secondary" title="Detail">
                    <i class="bi bi-eye"></i>
                  </a>
                  @if($req->status === 'approved')
                    <a href="{{ route('download-requests.download', $req) }}"
                       class="btn btn-sm btn-success" title="Download">
                      <i class="bi bi-download"></i>
                    </a>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($requests->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
          <small class="text-muted">{{ $requests->firstItem() }}–{{ $requests->lastItem() }} dari {{ $requests->total() }}</small>
          {{ $requests->links() }}
        </div>
      @endif
    @endif
  </div>
</div>
@endsection

@push('styles')
<style>
  .badge-sangat-rahasia{background:#fee2e2;color:#7f1d1d;}
  .badge-rahasia{background:#fef3c7;color:#92400e;}
  .badge-terbatas{background:#dbeafe;color:#1e3a8a;}
  .badge-biasa{background:#d1fae5;color:#065f46;}
  .page-link{color:var(--esdm-navy);border-color:#d4c9a8;font-size:13px;}
  .page-item.active .page-link{background:var(--esdm-navy);border-color:var(--esdm-navy);}
</style>
@endpush
