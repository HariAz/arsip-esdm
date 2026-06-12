<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    // Konstanta kode aksi
    const ACTION_USER_LOGIN             = 'user.login';
    const ACTION_USER_LOGOUT            = 'user.logout';
    const ACTION_DOCUMENT_UPLOAD        = 'document.upload';
    const ACTION_DOCUMENT_UPDATED       = 'document.updated';
    const ACTION_DOCUMENT_DELETED       = 'document.deleted';
    const ACTION_DOCUMENT_VIEW          = 'document.view';
    const ACTION_DOCUMENT_SEARCH        = 'document.search';
    const ACTION_DOWNLOAD_REQUESTED     = 'download.requested';
    const ACTION_DOWNLOAD_MAGIC_SENT    = 'download.magic_sent';
    const ACTION_DOWNLOAD_APPROVED_STEP = 'download.approved_step';
    const ACTION_DOWNLOAD_REJECTED_STEP = 'download.rejected_step';
    const ACTION_DOWNLOAD_COMPLETED     = 'download.completed';
    const ACTION_DOWNLOAD_EXECUTED      = 'download.executed';
    const ACTION_UPLOAD_APPROVAL_SENT   = 'upload.approval_sent';
    const ACTION_UPLOAD_APPROVED        = 'upload.approved';
    const ACTION_UPLOAD_REJECTED        = 'upload.rejected';
    const ACTION_DIVISION_CREATED       = 'division.created';
    const ACTION_DIVISION_UPDATED       = 'division.updated';
    const ACTION_DIVISION_TOGGLED       = 'division.toggled';
    const ACTION_DIVISION_DELETED       = 'division.deleted';

    // Tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'document_id', 'action', 'description',
        'actor_name', 'actor_email', 'ip_address', 'user_agent', 'metadata',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    // Helper statis untuk mudah mencatat log
    public static function record(
        string $action,
        ?int $userId = null,
        ?int $documentId = null,
        ?string $description = null,
        array $metadata = [],
        ?string $actorName = null,
        ?string $actorEmail = null
    ): self {
        return self::create([
            'action'      => $action,
            'user_id'     => $userId,
            'document_id' => $documentId,
            'description' => $description,
            'metadata'    => $metadata ?: null,
            'actor_name'  => $actorName,
            'actor_email' => $actorEmail,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}