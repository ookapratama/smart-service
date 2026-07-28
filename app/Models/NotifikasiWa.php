<?php

namespace App\Models;

use App\Enums\NotifikasiWaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiWa extends Model
{
    protected $table = 'notifikasi_wa';

    protected $fillable = [
        'tiket_id',
        'phone',
        'pesan',
        'status',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'status' => NotifikasiWaStatus::class,
        'sent_at' => 'datetime',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class);
    }
}
