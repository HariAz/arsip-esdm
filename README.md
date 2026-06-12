# Sistem Arsip Dokumen Digital
## Kementerian ESDM
### Referensi: Kepmen ESDM No. 167.K/04/MEM/2020

---

## 📋 Cara Pakai File Ini

Folder ini berisi semua file kustom untuk project Laravel.
**Bukan** project Laravel yang lengkap — hanya file yang perlu di-copy ke project baru.

---

## 🚀 Langkah Setup (Urutan Wajib)

### 1. Buat Project Laravel Baru
```bash
cd C:\xampp\htdocs
composer create-project laravel/laravel arsip-esdm "^10.0"
cd arsip-esdm
```

### 2. Copy Semua File dari Folder Ini
Copy isi folder `arsip-esdm` dari zip ini ke dalam folder project Laravel.
Timpa file yang sudah ada jika diminta.

**PENTING:** Untuk folder `database/migrations/`, hapus dulu file migration
bawaan Laravel (users, password_reset, dll) sebelum copy file kita.

### 3. Install Dependencies Tambahan
```bash
composer require smalot/pdfparser
composer require barryvdh/laravel-dompdf
```

### 4. Buat Database
- Buka http://localhost/phpmyadmin
- New → nama: `arsip_esdm` → Collation: `utf8mb4_unicode_ci` → Create

### 5. Setting .env
Buka file `.env` di root project, ubah:
```env
APP_NAME="Sistem Arsip ESDM"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsip_esdm
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=arsip@esdm.go.id
MAIL_FROM_NAME="Sistem Arsip ESDM"

QUEUE_CONNECTION=database
```

### 6. Generate Key
```bash
php artisan key:generate
```

### 7. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 8. Storage Link
```bash
php artisan storage:link
```

### 9. Setup Queue (untuk email magic link)
```bash
php artisan queue:table
php artisan migrate
```

### 10. Jalankan Aplikasi
Buka **2 terminal terpisah**:

Terminal 1 (server):
```bash
php artisan serve
```

Terminal 2 (queue worker untuk email):
```bash
php artisan queue:work
```

Buka browser: http://127.0.0.1:8000

---

## 🔑 Login Default (dari Seeder)

| Email | Password |
|---|---|
| arsiparis1@esdm.go.id | password123 |
| arsiparis2@esdm.go.id | password123 |
| arsiparis3@esdm.go.id | password123 |

---

## 📁 Struktur File dalam Zip Ini

```
arsip-esdm/
├── app/
│   ├── Http/Controllers/
│   │   ├── ActivityLogController.php
│   │   ├── ApprovalController.php
│   │   ├── AuthController.php
│   │   ├── DivisionController.php
│   │   ├── DocumentController.php
│   │   └── DownloadRequestController.php
│   ├── Jobs/
│   │   ├── SendDownloadApprovalMagicLink.php
│   │   └── SendUploadApprovalMagicLink.php
│   ├── Mail/
│   │   ├── DownloadApprovalMagicLinkMail.php
│   │   └── UploadApprovalMagicLinkMail.php
│   └── Models/
│       ├── ActivityLog.php
│       ├── Division.php
│       ├── Document.php
│       ├── DocumentFullText.php
│       ├── DownloadApproval.php (dalam DownloadRequest)
│       ├── DownloadApprovalStep.php
│       ├── DownloadRequest.php
│       ├── UploadApproval.php
│       ├── UploadApprovalStep.php
│       └── User.php
├── database/
│   ├── migrations/         ← HAPUS migration bawaan Laravel dulu!
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_divisions_table.php
│   │   ├── ..._create_documents_table.php
│   │   ├── ..._create_document_full_texts_table.php
│   │   ├── ..._create_upload_approvals_table.php
│   │   ├── ..._create_download_requests_table.php
│   │   └── ..._create_activity_logs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── activity-logs/index.blade.php
│   ├── approvals/
│   │   ├── already-used.blade.php
│   │   ├── download.blade.php
│   │   ├── expired.blade.php
│   │   └── success.blade.php
│   ├── auth/login.blade.php
│   ├── divisions/
│   │   ├── _form.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── documents/
│   │   ├── create.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── download-requests/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── emails/
│   │   ├── download-approval-magic-link.blade.php
│   │   └── upload-approval-magic-link.blade.php
│   └── layouts/app.blade.php
└── routes/web.php
```

---

## ⚠️ Catatan Penting

- Queue worker (`php artisan queue:work`) **harus aktif** agar email magic link terkirim
- File PDF tersimpan di `storage/app/documents/` — jangan dihapus
- Seeder berisi 3 user default dan 8 divisi contoh — **ganti data divisi** dengan data asli instansi
