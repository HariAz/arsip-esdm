{{-- resources/views/approvals/expired.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Link Kedaluwarsa</title>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { font-family:'Source Sans 3',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
  .card-custom { background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); max-width:440px; width:100%; text-align:center; padding:48px 36px; }
  .icon { font-size:3.5rem; margin-bottom:16px; }
  h2 { font-size:22px; color:#0d2b4e; margin-bottom:12px; }
  p  { font-size:14px; color:#6b7280; line-height:1.7; }
  .footer { font-size:11px; color:#9ca3af; margin-top:24px; }
</style>
</head>
<body>
<div class="card-custom">
  <div class="icon">⏰</div>
  <h2>Link Sudah Kedaluwarsa</h2>
  <p>
    Link persetujuan ini sudah tidak berlaku karena melebihi batas waktu 72 jam.
    Silakan hubungi arsiparis untuk meminta pengiriman ulang link persetujuan.
  </p>
  <p class="footer">Sistem Arsip Dokumen Digital — Kementerian ESDM</p>
</div>
</body>
</html>
