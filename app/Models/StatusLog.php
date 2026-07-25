<?php

namespace App\Models;

use App\Enums\TiketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    protected $table = 'status_log';

    protected $fillable = [
        'tiket_id',
        'status_from',
        'status_to',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'status_from' => TiketStatus::class,
        'status_to' => TiketStatus::class,
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
