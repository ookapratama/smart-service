<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelayanan extends Model
{
    protected $table = 'jadwal_pelayanan';

    protected $fillable = [
        'kelurahan_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'istirahat',
        'petugas',
        'telepon',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'hari' => 'array',
        'is_active' => 'boolean',
    ];

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }

    /**
     * Scope: hanya jadwal yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
