<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = [
        'name', 'code', 'head_name', 'head_email', 'head_phone',
        'general_affairs_name', 'general_affairs_email', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Scope hanya divisi aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}