<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Upload Dokumen</title>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background:#f0f4f8; margin:0; padding:24px; color:#1a202c; }
  .container { max-width:560px; margin:0 auto; }
  .header { background:#0d2b4e; border-radius:8px 8px 0 0; padding:28px 32px; text-align:center; }
  .header h1 { color:#c8972a; font-size:20px; margin:0 0 4px; }
  .header p  { color:rgba(255,255,255,.6); font-size:13px; margin:0; }
  .body { background:#fff; padding:32px; border-left:1px solid #e2e8f0; border-right:1px solid #e2e8f0; }
  .body p { font-size:14px; line-height:1.7; color:#374151; margin:0 0 14px; }
  .doc-card { background:#f8f7f4; border:1px solid #d4c9a8; border-radius:8px; padding:16px 20px; margin:20px 0; }
  .doc-card .lbl { font-size:11px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; margin-top:10px; }
  .doc-card .lbl:first-child { margin-top:0; }
  .doc-card .val { font-size:14px; color:#0d2b4e; font-weight:600; }
  .status-box-ok  { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; border-radius:8px; padding:14px 20px; margin:20px 0; }
  .status-box-err { background:#fee2e2; color:#7f1d1d; border:1px solid #fca5a5; border-radius:8px; padding:14px 20px; margin:20px 0; }
  .status-title { font-size:15px; font-weight:700; margin-bottom:6px; }
  .reason-box { background:#fff7ed; border-left:4px solid #f97316; padding:12px 16px; border-radius:0 6px 6px 0; font-size:13px; color:#9a3412; margin-top:12px; }
  .step-row { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; font-size:13px; color:#374151; }
  .divider { border:none; border-top:1px solid #e5e7eb; margin:20px 0; }
  .footer { background:#f8f7f4; border:1px solid #e2e8f0; border-radius:0 0 8px 8px; padding:16px 32px; text-align:center; font-size:11px; color:#9ca3af; }
</style>
</head>
<body>
<div class="container">

  <div class="header">
    <h1>🗄️ Sistem Arsip ESDM</h1>
    <p>Notifikasi Status Upload Dokumen Sangat Rahasia</p>
  </div>

  <div class="body">
    <p>Yth. <strong>{{ $uploadApproval->requester->name }}</strong>,</p>
    <p>Berikut adalah pembaruan status dokumen Sangat Rahasia yang Anda ajukan untuk diarsipkan:</p>

    <div class="doc-card">
      <div class="lbl">Nomor Surat</div>
      <div class="val">{{ $uploadApproval->document->document_number }}</div>
      <div class="lbl">Perihal / Judul</div>
      <div class="val">{{ $uploadApproval->document->title }}</div>
      <div class="lbl">Tanggal Dokumen</div>
      <div class="val">{{ $uploadApproval->document->document_date->translatedFormat('d F Y') }}</div>
      <div class="lbl">Divisi</div>
      <div class="val">{{ $uploadApproval->document->division->name ?? '-' }}</div>
    </div>

    @if($result === 'approved')

      <div class="status-box-ok">
        <div class="status-title">✅ Dokumen Telah Disetujui &amp; Aktif</div>
        Semua pihak telah menyetujui upload dokumen ini.
        Dokumen kini <strong>aktif</strong> dan tersedia di sistem arsip.
      </div>

      @php $approvedSteps = $uploadApproval->steps->where('status', 'approved'); @endphp
      @foreach($approvedSteps as $s)
      <div class="step-row">
        <span>✅</span>
        <div>
          <strong>Step {{ $s->step_order }} – {{ $s->step_label }}</strong>
          ({{ $s->approver_name }})<br>
          <span style="color:#6b7280;">
            Disetujui pada {{ $s->decided_at?->translatedFormat('d F Y, H:i') }} WIB
          </span>
        </div>
      </div>
      @endforeach

    @else

      @php $rejectedStep = $uploadApproval->steps->firstWhere('status', 'rejected'); @endphp
      <div class="status-box-err">
        <div class="status-title">❌ Dokumen Ditolak</div>
        Dokumen ditolak pada Step {{ $rejectedStep?->step_order ?? '-' }}
        oleh <strong>{{ $rejectedStep?->approver_name ?? '-' }}</strong>
        ({{ $rejectedStep?->step_label ?? '-' }}).
        Dokumen tidak akan masuk arsip.
      </div>

      @if($rejectedStep?->rejection_reason)
      <div class="reason-box">
        <strong>Alasan Penolakan:</strong><br>
        {{ $rejectedStep->rejection_reason }}
      </div>
      @endif

      <p style="margin-top:16px; font-size:13px;">
        Jika Anda merasa ada kekeliruan, silakan hubungi {{ $rejectedStep?->approver_name ?? 'pihak terkait' }}
        atau upload ulang dokumen setelah diperbaiki.
      </p>

    @endif

    <hr class="divider">
    <p style="font-size:12px; color:#6b7280; margin:0;">
      Email ini dikirim otomatis saat proses approval selesai pada
      {{ $uploadApproval->completed_at?->translatedFormat('d F Y, H:i') }} WIB.
    </p>
  </div>

  <div class="footer">
    Sistem Arsip Dokumen Digital &mdash; Kementerian ESDM<br>
    Email ini dikirim otomatis oleh sistem. Jangan membalas email ini.
  </div>

</div>
</body>
</html>
