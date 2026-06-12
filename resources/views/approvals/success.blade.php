{{-- resources/views/approvals/success.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keputusan Dicatat — Sistem Arsip ESDM</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  :root { --navy:#0d2b4e; --gold:#c8972a; }
  body { font-family:'Source Sans 3',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
  .result-card { background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); max-width:480px; width:100%; text-align:center; padding:48px 36px; }
  .icon-circle { width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 20px; }
  .icon-circle.approve { background:#dcfce7; color:#16a34a; }
  .icon-circle.reject  { background:#fee2e2; color:#dc2626; }
  h2 { font-size:22px; color:var(--navy); margin-bottom:12px; font-weight:700; }
  p  { font-size:14px; color:#6b7280; line-height:1.7; }
  .meta { background:#f8f7f4; border-radius:8px; padding:12px 16px; font-size:12px; color:#6b7280; margin-top:20px; text-align:left; }
  .footer { font-size:11px; color:#9ca3af; margin-top:24px; }
</style>
</head>
<body>
<div class="result-card">
  <div class="icon-circle {{ $action === 'approved' ? 'approve' : 'reject' }}">
    @if($action === 'approved') ✅ @else ❌ @endif
  </div>
  <h2>
    @if($action === 'approved') Persetujuan Dicatat @else Penolakan Dicatat @endif
  </h2>
  <p>{{ $message }}</p>
  <div class="meta">
    <strong>Dicatat pada:</strong> {{ now()->translatedFormat('d F Y, H:i:s') }}<br>
    <strong>Approver:</strong> {{ $step->approver_name }}<br>
    <strong>Step:</strong> {{ $step->step_order }} dari 2 ({{ $step->step_label }})
  </div>
  <p class="footer">
    Halaman ini dapat ditutup.<br>
    Sistem Arsip Dokumen Digital — Kementerian ESDM
  </p>
</div>
</body>
</html>
