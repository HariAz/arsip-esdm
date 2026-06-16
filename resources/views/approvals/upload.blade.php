<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Persetujuan Upload — Sistem Arsip ESDM</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  :root { --navy:#0d2b4e; --gold:#c8972a; }
  body { font-family:'Source Sans 3',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
  .approval-card { background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); max-width:560px; width:100%; overflow:hidden; }
  .card-header-custom { background:var(--navy); padding:24px 28px; }
  .card-header-custom h1 { color:var(--gold); font-size:18px; margin:0 0 4px; font-weight:700; }
  .card-header-custom p  { color:rgba(255,255,255,.6); font-size:12px; margin:0; }
  .card-body-custom { padding:28px; }
  .step-badge { display:inline-flex; align-items:center; gap:6px; background:#fef3c7; border:1px solid #fde68a; border-radius:20px; padding:4px 12px; font-size:12px; color:#92400e; font-weight:600; margin-bottom:16px; }
  .doc-info-table { width:100%; font-size:13px; }
  .doc-info-table td:first-child { color:#6b7280; font-weight:600; width:40%; padding:6px 0; }
  .doc-info-table td:last-child { color:var(--navy); font-weight:600; padding:6px 0; }
  .prev-step-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:10px 14px; font-size:12px; color:#166534; margin:16px 0; }
  .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
  .badge-rahasia  { background:#fef3c7; color:#92400e; }
  .badge-terbatas { background:#dbeafe; color:#1e3a8a; }
  .badge-biasa    { background:#d1fae5; color:#065f46; }
  .divider-gold { height:3px; background:linear-gradient(90deg,var(--gold),rgba(200,151,42,.2)); border-radius:2px; margin:20px 0; }
  .warning-box { background:#fff7ed; border:1px solid #fed7aa; border-radius:6px; padding:12px 14px; font-size:12px; color:#9a3412; margin:16px 0; }
</style>
</head>
<body>

<div class="approval-card">
  <div class="card-header-custom">
    <h1>🗄️ Sistem Arsip ESDM</h1>
    <p>
      @if($action === 'approve') Konfirmasi Persetujuan Upload Dokumen Rahasia
      @else Konfirmasi Penolakan Upload Dokumen Rahasia
      @endif
    </p>
  </div>

  <div class="card-body-custom">

    <div class="step-badge">
      <i class="bi bi-shield-lock"></i>
      Step {{ $step->step_order }} dari 2 —
      {{ $step->step_order === 1 ? 'Bagian Umum' : 'Kepala Divisi' }}
    </div>

    <p style="font-size:14px; color:#374151; margin-bottom:16px;">
      Yth. <strong>{{ $step->approver_name }}</strong>,<br>
      Berikut dokumen <strong>Sangat Rahasia</strong> yang menunggu persetujuan Anda sebelum dapat masuk ke arsip:
    </p>

    {{-- Info dokumen --}}
    <div class="card mb-3" style="border-color:#fecaca;">
      <div class="card-body py-3">
        <table class="doc-info-table">
          <tr>
            <td>Nomor Surat</td>
            <td class="font-monospace">{{ $step->uploadApproval->document->document_number }}</td>
          </tr>
          <tr>
            <td>Perihal</td>
            <td>{{ $step->uploadApproval->document->title }}</td>
          </tr>
          <tr>
            <td>Tanggal Dokumen</td>
            <td>{{ $step->uploadApproval->document->document_date->translatedFormat('d F Y') }}</td>
          </tr>
          <tr>
            <td>Divisi</td>
            <td>{{ $step->uploadApproval->document->division->name ?? '-' }}</td>
          </tr>
          <tr>
            <td>Klasifikasi</td>
            <td>
              <span class="badge badge-sangat-rahasia px-2">Sangat Rahasia</span>
            </td>
          </tr>
          <tr>
            <td>Diunggah Oleh</td>
            <td>{{ $step->uploadApproval->requester->name ?? '-' }}</td>
          </tr>
          <tr>
            <td>Waktu Upload</td>
            <td>{{ $step->uploadApproval->requested_at?->translatedFormat('d F Y, H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>

    {{-- Bukti step sebelumnya (hanya untuk step 2) --}}
    @if($prevStep && $prevStep->status === 'approved')
      <div class="prev-step-box">
        ✅ <strong>Bagian Umum telah menyetujui</strong> pada
        {{ $prevStep->decided_at?->translatedFormat('d F Y, H:i') }}
      </div>
    @endif

    <div class="warning-box">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>
      <strong>Perhatian:</strong> Dokumen ini diklasifikasikan <strong>Sangat Rahasia</strong>.
      Pastikan Anda memiliki kewenangan untuk memberikan persetujuan ini.
    </div>

    <div class="divider-gold"></div>

    {{-- Form keputusan --}}
    <form method="POST" action="{{ route('approval.upload.decide', [$step->token, $action]) }}">
      @csrf

      @if($action === 'reject')
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:13px;">
            Alasan Penolakan <span class="text-danger">*</span>
          </label>
          <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror"
            rows="3" placeholder="Tuliskan alasan penolakan dokumen ini..." required></textarea>
          @error('rejection_reason')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      @endif

      @if($action === 'approve')
        <div class="alert alert-warning d-flex gap-2 mb-3" style="font-size:13px;">
          <i class="bi bi-shield-check flex-shrink-0 mt-1"></i>
          <span>Dengan mengklik <strong>Setujui</strong>, Anda menyetujui bahwa dokumen Sangat Rahasia ini layak diarsipkan. Keputusan beserta waktu dan IP Anda akan tercatat.</span>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="background:var(--navy); border-color:var(--navy);">
          <i class="bi bi-shield-check me-2"></i> Setujui Pengarsipan Dokumen
        </button>
      @else
        <div class="alert alert-danger d-flex gap-2 mb-3" style="font-size:13px;">
          <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
          <span>Penolakan ini bersifat final. Dokumen tidak akan masuk ke arsip dan arsiparis perlu mengupload ulang.</span>
        </div>
        <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
          <i class="bi bi-x-circle me-2"></i> Tolak Pengarsipan Dokumen
        </button>
      @endif

      <a href="{{ route('approval.upload.show', [$step->token, $action === 'approve' ? 'reject' : 'approve']) }}"
         class="btn btn-outline-secondary w-100 mt-2">
        <i class="bi bi-arrow-left me-1"></i>
        {{ $action === 'approve' ? 'Saya ingin menolak' : 'Saya ingin menyetujui' }}
      </a>
    </form>

  </div>

  <div class="text-center pb-3" style="font-size:11px; color:#9ca3af;">
    Sistem Arsip Dokumen Digital &mdash; Kementerian ESDM
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
