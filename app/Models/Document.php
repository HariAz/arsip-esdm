<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use SoftDeletes;

    // Konstanta klasifikasi (Kepmen ESDM No. 167.K/04/MEM/2020)
    const CLASSIFICATION_BIASA          = 'biasa';
    const CLASSIFICATION_TERBATAS       = 'terbatas';
    const CLASSIFICATION_RAHASIA        = 'rahasia';
    const CLASSIFICATION_SANGAT_RAHASIA = 'sangat_rahasia';

    // Konstanta status
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_ACTIVE           = 'active';
    const STATUS_REJECTED         = 'rejected';
    const STATUS_ARCHIVED         = 'archived';

    protected $fillable = [
        'division_id', 'uploaded_by', 'document_number', 'title',
        'document_date', 'year', 'document_type', 'document_classification_code',
        'classification', 'file_path', 'file_name', 'file_size', 'file_hash', 'status',
    ];

    protected $casts = [
        'document_date' => 'date',
        'file_size'     => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function fullText(): HasOne
    {
        return $this->hasOne(DocumentFullText::class);
    }

    public function uploadApproval(): HasOne
    {
        return $this->hasOne(UploadApproval::class);
    }

    public function downloadRequests(): HasMany
    {
        return $this->hasMany(DownloadRequest::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helper ──────────────────────────────────────

    // Apakah dokumen bisa langsung didownload tanpa approval?
    public function isFreeDownload(): bool
    {
        return $this->classification === self::CLASSIFICATION_BIASA;
    }

    // Apakah dokumen bisa di-preview PDF?
    public function isPreviewable(): bool
    {
        return in_array($this->classification, [
            self::CLASSIFICATION_BIASA,
            self::CLASSIFICATION_TERBATAS,
        ]);
    }

    // Apakah full-text search aktif untuk dokumen ini?
    public function hasFullText(): bool
    {
        return in_array($this->classification, [
            self::CLASSIFICATION_BIASA,
            self::CLASSIFICATION_TERBATAS,
        ]);
    }

    // Label klasifikasi untuk tampilan UI
    public function getClassificationLabelAttribute(): string
    {
        return match($this->classification) {
            self::CLASSIFICATION_BIASA          => 'Biasa',
            self::CLASSIFICATION_TERBATAS       => 'Terbatas',
            self::CLASSIFICATION_RAHASIA        => 'Rahasia',
            self::CLASSIFICATION_SANGAT_RAHASIA => 'Sangat Rahasia',
            default                             => 'Tidak Diketahui',
        };
    }

    // Warna badge klasifikasi (untuk UI)
    public function getClassificationColorAttribute(): string
    {
        return match($this->classification) {
            self::CLASSIFICATION_BIASA          => 'success',
            self::CLASSIFICATION_TERBATAS       => 'info',
            self::CLASSIFICATION_RAHASIA        => 'warning',
            self::CLASSIFICATION_SANGAT_RAHASIA => 'danger',
            default                             => 'secondary',
        };
    }

    // Format ukuran file (bytes → KB/MB)
    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        if ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 1) . ' KB';
        }
        return round($this->file_size / (1024 * 1024), 2) . ' MB';
    }

    // ── Scopes ──────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByClassification($query, $classification)
    {
        return $query->where('classification', $classification);
    }
}