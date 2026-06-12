<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DownloadRequest extends Model
{
    protected $fillable = [
        'document_id', 'requested_by', 'reason', 'status',
        'requested_at', 'completed_at', 'downloaded_at',
    ];

    protected $casts = [
        'requested_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(DownloadApprovalStep::class)->orderBy('step_order');
    }

    public function currentStep()
    {
        return $this->steps()
            ->whereIn('status', ['waiting', 'sent'])
            ->orderBy('step_order')
            ->first();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}