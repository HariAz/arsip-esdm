@extends('layouts.app')
@section('title', 'Detail Permintaan Download')
@section('page-title', 'Detail Permintaan Download')

@section('content')
<div class="gold-line"></div>

<div class="row g-4">

  {{-- ── KIRI: Info & Status ── --}}
  <div class="col-lg-7">
    <div class="card mb-3">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-file-earmark-text"></i> Dokumen yang Diminta
      </div>
      <div class="card-body">
        <dl class="row req-info">
          <dt class="col-sm-4">Nomor Surat</dt>
          <dd class="col-sm-8 font-monospace">{{ $downloadRequest->document->document_number }}</dd>
          <dt class="col-sm-4">Perihal</dt>
          <dd class="col-sm-8 fw-semibold">{{ $downloadRequest->document->title }}</dd>
          <dt class="col-sm-4">Tanggal</dt>
          <dd class="col-sm-8">{{ $downloadRequest->document->document_date->translatedFormat('d F Y') }}</dd>
          <dt class="col-sm-4">Divisi</dt>
          <dd class="col-sm-8">{{ $downloadRequest->document->division->name ?? '-' }}</dd>
          <dt class="col-sm-4">Klasifikasi</dt>
          <dd class="col-sm-8">
            @php $cls = $downloadRequest->document->classification; @endphp
            <span class="badge badge-{{ str_replace('_','-',$cls) }} px-2">
              {{ $downloadRequest->document->classification_label }}
            </span>
          </dd>
          <dt class="col-sm-4">Alasan</dt>
          <dd class="col-sm-8">{{ $downloadRequest->reason ?? '<em class="text-muted">—</em>' }}</dd>
          <dt class="col-sm-4">Diajukan</dt>
          <dd class="col-sm-8">{{ $downloadRequest->requested_at->translatedFormat('d F Y, H:i') }}</dd>
        </dl>
      </div>
    </div>

    {{-- Tombol download jika sudah approved --}}
    @if($downloadRequest->status === 'approved')
      <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
          <strong>Permintaan disetujui!</strong> Dokumen siap untuk diunduh.
        </div>
      </div>
      <a href="{{ route('download-requests.download', $downloadRequest) }}"
         class="btn btn-success w-100 py-3 fw-bold mb-3">
        <i class="bi bi-download me-2"></i>Download Dokumen Sekarang
      </a>
    @elseif($downloadRequest->status === 'rejected')
      <div class="alert alert-danger d-flex gap-2">
        <i class="bi bi-x-circle-fill fs-5 flex-shrink-0"></i>
        <div>
          <strong>Permintaan ditolak.</strong>
          @php $rejectedStep = $downloadRequest->steps->firstWhere('status','rejected'); @endphp
          @if($rejectedStep?->rejection_reason)
            <br><small>Alasan: {{ $rejectedStep->rejection_reason }}</small>
          @endif
        </div>
      </div>
    @endif
  </div>

  {{-- ── KANAN: Timeline Approval ── --}}
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3"></i> Progress Approval
      </div>
      <div class="card-body">

        <div class="timeline">
          {{-- Step 0: Request dibuat --}}
          <div class="tl-item done">
            <div class="tl-dot done"><i class="bi bi-person-plus"></i></div>
            <div class="tl-content">
              <div class="tl-title">Permintaan Diajukan</div>
              <div class="tl-meta">{{ $downloadRequest->requested_at->translatedFormat('d M Y, H:i') }}</div>
              <div class="tl-meta">oleh {{ $downloadRequest->requester->name }}</div>
            </div>
          </div>

          {{-- Step 1 & 2 --}}
          @foreach($downloadRequest->steps->sortBy('step_order') as $step)
          <div class="tl-item {{ in_array($step->status, ['approved','rejected']) ? ($step->status === 'approved' ? 'done' : 'rejected') : ($step->status === 'sent' ? 'active' : 'waiting') }}">
            <div class="tl-dot {{ in_array($step->status, ['approved','rejected']) ? ($step->status === 'approved' ? 'done' : 'rejected') : ($step->status === 'sent' ? 'active' : 'waiting') }}">
              @if($step->status === 'approved') <i class="bi bi-check-lg"></i>
              @elseif($step->status === 'rejected') <i class="bi bi-x-lg"></i>
              @elseif($step->status === 'sent') <i class="bi bi-hourglass-split"></i>
              @else <i class="bi bi-clock"></i>
              @endif
            </div>
            <div class="tl-content">
              <div class="tl-title">Step {{ $step->step_order }}: {{ $step->step_label }}</div>
              <div class="tl-meta">{{ $step->approver_name }}</div>
              @if($step->status === 'sent')
                <div class="tl-meta text-warning">⏳ Menunggu keputusan</div>
                <div class="tl-meta" style="font-size:11px;">
                  Link kedaluwarsa: {{ $step->token_expires_at->translatedFormat('d M Y, H:i') }}
                </div>
              @elseif($step->status === 'approved')
                <div class="tl-meta text-success">✅ Disetujui: {{ $step->decided_at?->translatedFormat('d M Y, H:i') }}</div>
              @elseif($step->status === 'rejected')
                <div class="tl-meta text-danger">❌ Ditolak: {{ $step->decided_at?->translatedFormat('d M Y, H:i') }}</div>
                @if($step->rejection_reason)
                  <div class="tl-meta text-danger" style="font-size:11px;">Alasan: {{ $step->rejection_reason }}</div>
                @endif
              @else
                <div class="tl-meta text-muted">Menunggu step sebelumnya</div>
              @endif
            </div>
          </div>
          @endforeach

          {{-- Final --}}
          @if($downloadRequest->status === 'approved' || $downloadRequest->status === 'downloaded')
          <div class="tl-item done">
            <div class="tl-dot done"><i class="bi bi-check-all"></i></div>
            <div class="tl-content">
              <div class="tl-title">Semua Approval Selesai</div>
              <div class="tl-meta">{{ $downloadRequest->completed_at?->translatedFormat('d M Y, H:i') }}</div>
            </div>
          </div>
          @endif
        </div>

        {{-- Tombol kirim ulang jika ada token expired --}}
        @if($downloadRequest->status === 'pending')
          @php
            $hasExpired = $downloadRequest->steps->contains(fn($s) =>
              in_array($s->status, ['sent','waiting']) && $s->token_expires_at->isPast()
            );
          @endphp
          @if($hasExpired)
            <div class="mt-3 pt-3 border-top">
              <form method="POST" action="{{ route('download-requests.resend', $downloadRequest) }}">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                  <i class="bi bi-send me-1"></i>Kirim Ulang Magic Link
                </button>
              </form>
            </div>
          @endif
        @endif

      </div>
    </div>
  </div>

</div>

<div class="mt-4">
  <a href="{{ route('download-requests.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
  </a>
</div>
@endsection

@push('styles')
<style>
  .req-info dt { font-weight:600; font-size:13px; color:#6b7280; }
  .req-info dd { font-size:13px; color:var(--esdm-navy); margin-bottom:10px; }
  .badge-sangat-rahasia{background:#fee2e2;color:#7f1d1d;}
  .badge-rahasia{background:#fef3c7;color:#92400e;}
  .badge-terbatas{background:#dbeafe;color:#1e3a8a;}
  .badge-biasa{background:#d1fae5;color:#065f46;}

  /* Timeline */
  .timeline { position:relative; padding-left:32px; }
  .timeline::before { content:''; position:absolute; left:13px; top:0; bottom:0; width:2px; background:#e5e7eb; }
  .tl-item { position:relative; margin-bottom:20px; }
  .tl-item:last-child { margin-bottom:0; }
  .tl-dot { position:absolute; left:-32px; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; border:2px solid #e5e7eb; background:#fff; }
  .tl-dot.done     { background:#dcfce7; border-color:#16a34a; color:#16a34a; }
  .tl-dot.active   { background:#fef9c3; border-color:#ca8a04; color:#ca8a04; }
  .tl-dot.rejected { background:#fee2e2; border-color:#dc2626; color:#dc2626; }
  .tl-dot.waiting  { background:#f3f4f6; border-color:#d1d5db; color:#9ca3af; }
  .tl-title { font-size:13px; font-weight:700; color:var(--esdm-navy); }
  .tl-meta  { font-size:12px; color:#6b7280; }
</style>
@endpush
