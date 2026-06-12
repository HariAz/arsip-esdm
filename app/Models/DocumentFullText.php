<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentFullText extends Model
{
    protected $fillable = ['document_id', 'content', 'extracted_at'];

    protected $casts = ['extracted_at' => 'datetime'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    // Scope untuk full-text search MySQL MATCH...AGAINST
    public function scopeSearch($query, string $keyword)
    {
        return $query->whereRaw(
            'MATCH(content) AGAINST(? IN BOOLEAN MODE)',
            [$keyword . '*']
        );
    }
}