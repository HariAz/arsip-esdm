<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadApprovalStep extends Model
{
    protected $fillable = [
        'download_request_id', 'step_order', 'approver_name', 'approver_email',
        'token', 'token_expires_at', 'token_used_at',
        'status', 'decided_at', 'rejection_reason', 'ip_address',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'token_used_at'    => 'datetime',
        'decided_at'       => 'datetime',
    ];

    public function downloadRequest(): BelongsTo
    {
        return $this->belongsTo(DownloadRequest::class);
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return !is_null($this->token_used_at);
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed() && $this->status === 'sent';
    }

    public function getStepLabelAttribute(): string
    {
        return match($this->step_order) {
            1 => 'Bagian Umum',
            2 => 'Kepala Divisi',
            default => 'Step ' . $this->step_order,
        };
    }
}