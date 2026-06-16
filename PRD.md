# PRD — Sistem Arsip Dokumen Digital ESDM

**Versi:** 1.0  
**Tanggal Terakhir Diperbarui:** 16 Juni 2026  
**Repository:** https://github.com/HariAz/arsip-esdm  
**Stack:** Laravel 10 · PHP 8.1 · MySQL · Bootstrap 5 · XAMPP

---

## 1. Latar Belakang

Kementerian ESDM membutuhkan sistem pengarsipan dokumen digital internal yang:

- Menggantikan pengelolaan arsip manual berbasis kertas
- Mengklasifikasikan dokumen sesuai **Kepmen ESDM No. 167 K/04/MEM/2020**
- Menerapkan alur persetujuan multi-step untuk dokumen sensitif
- Mencatat seluruh aktivitas sistem untuk keperluan audit

---

## 2. Pengguna Sistem

| Peran | Akses |
|-------|-------|
| **Arsiparis** | Login ke sistem, upload/edit/hapus dokumen, ajukan download, lihat log |
| **Bagian Umum** | Approver Step 1 upload dokumen Sangat Rahasia (via magic link, tanpa login) |
| **Kepala Divisi** | Approver Step 2 upload & download dokumen (via magic link, tanpa login) |

---

## 3. Klasifikasi Dokumen

| Klasifikasi | Akses Download | Preview PDF | Upload Approval |
|------------|----------------|-------------|-----------------|
| Biasa | Langsung tanpa approval | ✅ | Langsung aktif |
| Terbatas | Perlu approval | ✅ | Langsung aktif |
| Rahasia | Perlu approval | ❌ | Langsung aktif |
| Sangat Rahasia | Perlu approval | ❌ | Wajib 2-step approval sebelum aktif |

Kode klasifikasi dokumen mengikuti **Kepmen ESDM No. 167 K/04/MEM/2020** (dipilih dari dropdown, bukan input bebas).

---

## 4. Fitur yang Sudah Diimplementasi

### 4.1 Autentikasi
- Login/logout berbasis session
- Middleware `auth` untuk semua route internal
- Catat waktu `last_login_at` setiap login

### 4.2 Dashboard
- 4 stat cards: Dokumen Aktif, Menunggu Approval, Permintaan Download, Total Pengguna
- Progress bar distribusi klasifikasi (Biasa / Terbatas / Rahasia / Sangat Rahasia)
- Tabel ringkasan status dokumen
- 8 log aktivitas terbaru
- 5 dokumen terbaru

### 4.3 Manajemen Dokumen
- Upload PDF (drag & drop, maks 20 MB)
- Metadata: nomor surat, judul, tanggal, divisi, jenis dokumen, kode klasifikasi, klasifikasi keamanan
- Ekstraksi teks PDF otomatis (untuk dokumen Biasa & Terbatas)
- Edit metadata (nomor surat & file tidak bisa diubah setelah upload)
- Soft delete → Trash → Restore / Force delete permanen
- Preview PDF inline (hanya Biasa & Terbatas)
- **Sortable columns** (Nomor Surat, Judul, Tanggal, Klasifikasi) — server-side
- **Bulk delete** dengan konfirmasi
- **Export CSV** dengan filter aktif (Excel-compatible, BOM UTF-8)
- Tab status: Aktif / Menunggu Approval / Ditolak / Trash

### 4.4 Upload Approval (Sangat Rahasia)
- Dokumen Sangat Rahasia tidak langsung aktif — masuk status `pending_approval`
- Sistem kirim magic link ke **Bagian Umum** divisi (Step 1)
- Setelah Step 1 approve → magic link dikirim ke **Kepala Divisi** (Step 2)
- Setelah semua step approve → dokumen otomatis aktif (`STATUS_ACTIVE`)
- Jika salah satu step reject → dokumen ditolak (`STATUS_REJECTED`)
- **Email notifikasi** dikirim ke arsiparis saat approval selesai (approve/reject)
- **Halaman status approval** (`/documents/{id}/upload-approval`): timeline per step, status real-time, alasan penolakan
- Token magic link berlaku **72 jam**
- **Rate limiting**: max 5 submit per menit per IP untuk POST approval

### 4.5 Download Approval
- Dokumen Terbatas/Rahasia/Sangat Rahasia perlu mengajukan permintaan download
- Alur multi-step magic link approval (sama dengan upload)
- **Email notifikasi** ke arsiparis saat download approval selesai
- Tombol resend magic link
- Setelah semua step approve → arsiparis bisa download dari halaman permintaan

### 4.6 Manajemen Pengguna
- CRUD pengguna (nama, email, password)
- Toggle aktif/nonaktif (tidak bisa nonaktifkan diri sendiri)
- Avatar inisial nama

### 4.7 Manajemen Divisi
- CRUD divisi beserta data Bagian Umum (nama + email) dan Kepala Divisi
- Toggle aktif/nonaktif
- Data Bagian Umum wajib diisi sebelum bisa upload dokumen Sangat Rahasia

### 4.8 Log Aktivitas
- Catat semua aksi sistem: login/logout, upload, edit, hapus, approval, download, pencarian
- Filter: kata kunci, jenis aksi, pengguna, rentang tanggal
- Tampilkan IP address + **user agent** (expandable per baris)
- Aktor eksternal (approver) vs aktor internal (arsiparis) dibedakan secara visual

### 4.9 Notifikasi In-App (Sidebar)
- Badge kuning di "Arsip Dokumen" → jumlah dokumen pending upload approval
- Badge merah di "Permintaan Download" → jumlah permintaan download pending

### 4.10 UI/UX
- **Mobile responsive sidebar** dengan hamburger toggle + overlay backdrop
- **Breadcrumb navigasi** di halaman: create, edit, show, upload-approval-status
- Tema warna kustom: navy ESDM + gold
- Bootstrap 5 dropdowns di tabel dokumen (aksi per baris)

### 4.11 Reminder Token Kadaluwarsa
- Artisan command: `php artisan arsip:resend-expired-links`
- Flag `--dry-run` untuk preview tanpa kirim
- Regenerasi token 72 jam baru + dispatch job kirim ulang magic link
- Dijadwalkan otomatis setiap hari pukul 08:00 (via Laravel scheduler)
- Output log ke `storage/logs/expired-links.log`

---

## 5. Arsitektur Teknis

### Stack
| Komponen | Detail |
|---------|--------|
| Framework | Laravel 10 |
| PHP | ^8.1 |
| Database | MySQL (via XAMPP) |
| Frontend | Bootstrap 5.3, Bootstrap Icons 1.11 |
| Email testing | Mailtrap sandbox SMTP |
| PDF parsing | `smalot/pdfparser` |
| PDF generation | `barryvdh/laravel-dompdf` |
| Queue | `QUEUE_CONNECTION=sync` (synchronous) |
| Storage | Local disk (`storage/app/documents/YYYY/KODE-DIVISI/`) |

### Database Tables Utama
| Tabel | Keterangan |
|-------|------------|
| `users` | Akun arsiparis |
| `divisions` | Data divisi + kontak approver |
| `documents` | Dokumen arsip (soft delete) |
| `document_full_texts` | Teks hasil ekstraksi PDF |
| `upload_approvals` | Header approval upload |
| `upload_approval_steps` | Per-step token & status upload |
| `download_requests` | Permintaan download |
| `download_approval_steps` | Per-step token & status download |
| `activity_logs` | Audit trail seluruh aksi sistem |

### Alur Email
```
Upload Sangat Rahasia
  → Job: SendUploadApprovalMagicLink (Step 1)
  → Approver Step 1 klik link → POST /approve/upload/{token}/approve
  → Job: SendUploadApprovalMagicLink (Step 2)
  → Approver Step 2 klik link → Dokumen aktif
  → Mail: UploadApprovalCompletedMail → Arsiparis

Reject di step mana pun
  → Dokumen ditolak
  → Mail: UploadApprovalCompletedMail (result: rejected) → Arsiparis
```

---

## 6. Routes Utama

```
GET  /                          → Dashboard
GET  /documents                 → Daftar dokumen (filter + sort + bulk)
GET  /documents/export          → Export CSV
DELETE /documents/bulk-delete   → Bulk soft delete
GET  /documents/{id}            → Detail dokumen
GET  /documents/{id}/upload-approval → Status approval upload
GET  /documents/create          → Form upload
POST /documents                 → Proses upload

GET  /approve/upload/{token}/{action}  → Halaman approval upload (publik)
POST /approve/upload/{token}/{action}  → Proses keputusan (throttle 5/menit)
GET  /approve/download/{token}/{action} → Halaman approval download (publik)
POST /approve/download/{token}/{action} → Proses keputusan (throttle 5/menit)

GET  /download-requests         → Daftar permintaan download
GET  /users                     → Manajemen pengguna
GET  /divisions                 → Manajemen divisi
GET  /activity-logs             → Log aktivitas
```

---

## 7. Cara Setup Proyek Baru (Clone)

```bash
git clone https://github.com/HariAz/arsip-esdm.git
cd arsip-esdm
composer install
cp .env.example .env
php artisan key:generate
```

Isi `.env`:
```
DB_DATABASE=arsip_esdm
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<mailtrap_username>
MAIL_PASSWORD=<mailtrap_password>
QUEUE_CONNECTION=sync
```

```bash
php artisan migrate --seed
php artisan serve
```

Untuk scheduler (opsional):
```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Yang Belum Ada (Future Scope)

- Role-based access control (admin vs arsiparis biasa)
- Versi dokumen / revision history
- Notifikasi real-time (WebSocket/Pusher)
- QR code pada dokumen cetak
- Integrasi LDAP/SSO untuk autentikasi
- Multi-bahasa (i18n)
- API publik untuk integrasi sistem lain
