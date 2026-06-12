<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Persetujuan Download Dokumen</title>
<style>
  body { font-family:'Segoe UI',Arial,sans-serif; background:#f0f4f8; margin:0; padding:24px; color:#1a202c; }
  .container { max-width:560px; margin:0 auto; }
  .header { background:#0d2b4e; border-radius:8px 8px 0 0; padding:28px 32px; text-align:center; }
  .header h1 { color:#c8972a; font-size:20px; margin:0 0 4px; }
  .header p  { color:rgba(255,255,255,.6); font-size:13px; margin:0; }
  .body { background:#fff; padding:32px; border-left:1px solid #e2e8f0; border-right:1px solid #e2e8f0; }
  .body p { font-size:14px; line-height:1.7; color:#374151; margin:0 0 16px; }
  .doc-card { background:#f8f7f4; border:1px solid #d4c9a8; border-radius:8px; padding:16px 20px; margin:20px 0; }
  .doc-card .label { font-size:11px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
  .doc-card .value { font-size:14px; color:#0d2b4e; font-weight:600; margin-bottom:8px; }
  .requester-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:10px 14px; font-size:13px; color:#1e40af; margin-bottom:16px; }
  .badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700; }
  .badge-terbatas { background:#dbeafe; color:#1e3a8a; }
  .badge-rahasia  { background:#fef3c7; color:#92400e; }
  .badge-sr       { background:#fee2e2; color:#7f1d1d; }
  .btn-approve { display:block; background:#0d2b4e; color:#fff !important; text-decoration:none; text-align:center; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:700; margin:24px 0 12px; }
  .btn-reject  { display:block; background:#fff; color:#dc2626 !important; text-decoration:none; text-align:center; padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600; border:2px solid #dc2626; margin-bottom:24px; }
  .prev-approval { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:10px 14px; font-size:12px; color:#166534; margin-bottom:16px; }
  .note { background:#fffaf0; border-left:4px solid #c8972a; padding:12px 16px; border-radius:0 6px 6px 0; font-size:12px; color:#744210; }
  .step-info { font-size:12px; color:#6b7280; text-align:center; margin-bottom:8px; }
  .divider { border:none; border-top:1px solid #e5e7eb; margin:24px 0; }
  .token-note { font-size:11px; color:#9ca3af; text-align:center; }
  .footer { background:#f8f7f4; border:1px solid #e2e8f0; border-radius:0 0 8px 8px; padding:16px 32px; text-align:center; font-size:11px; color:#9ca3af; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>🗄️ Sistem Arsip ESDM</h1>
    <p>Permintaan Persetujuan Download Dokumen</p>
  </div>

  <div class="body">
    <p>Yth. <strong>{{ $step->approver_name }}</strong>,</p>
    <p>
      Terdapat permintaan download dokumen yang memerlukan persetujuan Anda.
    </p>

    {{-- Info pemohon --}}
    <div class="requester-box">
      👤 Diajukan oleh <strong>{{ $downloadRequest->requester->name }}</strong>
      pada {{ $downloadRequest->requested_at->translatedFormat('d F Y, H:i') }}
      @if($downloadRequest->reason)
        <br>📝 Alasan: <em>{{ $downloadRequest->reason }}</em>
      @endif
    </div>

    {{-- Info dokumen --}}
    <div class="doc-card">
      <div class="label">Nomor Surat</div>
      <div class="value">{{ $downloadRequest->document->document_number }}</div>
      <div class="label">Perihal / Judul</div>
      <div class="value">{{ $downloadRequest->document->title }}</div>
      <div class="label">Tanggal Dokumen</div>
      <div class="value">{{ $downloadRequest->document->document_date->translatedFormat('d F Y') }}</div>
      <div class="label">Divisi</div>
      <div class="value">{{ $downloadRequest->document->division->name ?? '-' }}</div>
      <div class="label">Klasifikasi</div>
      <div class="value">
        @php $cls = $downloadRequest->document->classification; @endphp
        <span class="badge badge-{{ str_replace('_','-',$cls) }}">
          {{ $downloadRequest->document->classification_label }}
        </span>
      </div>
    </div>

    {{-- Bukti step sebelumnya (step 2) --}}
    @if($step->step_order === 2)
      @php $prevStep = $downloadRequest->steps->firstWhere('step_order', 1); @endphp
      @if($prevStep && $prevStep->status === 'approved')
        <div class="prev-approval">
          ✅ <strong>Bagian Umum telah menyetujui</strong> pada
          {{ $prevStep->decided_at?->translatedFormat('d F Y, H:i') }}
          ({{ $prevStep->approver_name }})
        </div>
      @endif
    @endif

    <div class="step-info">
      Anda adalah approver <strong>Step {{ $step->step_order }} dari 2</strong>
      ({{ $step->step_order === 1 ? 'Bagian Umum' : 'Kepala Divisi' }})
    </div>

    <a href="{{ url('/approve/download/'.$step->token.'/approve') }}" class="btn-approve">
      ✅ &nbsp; Setujui Permintaan Download
    </a>
    <a href="{{ url('/approve/download/'.$step->token.'/reject') }}" class="btn-reject">
      ❌ &nbsp; Tolak Permintaan Download
    </a>

    <div class="note">
      <strong>⚠️ Perhatian:</strong> Link ini hanya dapat digunakan <strong>satu kali</strong>
      dan akan kedaluwarsa dalam <strong>72 jam</strong>.
    </div>

    <hr class="divider">
    <p class="token-note">
      Jika tombol tidak berfungsi, salin link ini ke browser:<br>
      <code style="font-size:10px; word-break:break-all;">
        {{ url('/approve/download/'.$step->token.'/approve') }}
      </code>
    </p>
  </div>

  <div class="footer">
    Sistem Arsip Dokumen Digital &mdash; Kementerian ESDM<br>
    Email ini dikirim otomatis. Jangan membalas email ini.
  </div>
</div>
</body>
</html>
