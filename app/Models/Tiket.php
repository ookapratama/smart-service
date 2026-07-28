<?php

namespace App\Models;

use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Format nomor_tiket: {prefix}-{YYMM}-{seq %05d}, mis. SRG-2607-00123.
 * Sequence dijaga race-safe oleh tabel tiket_counters (lihat TiketService::generateNomorTiket()).
 */
class Tiket extends Model
{
    use LogsActivity;

    public const NOMOR_PREFIX = 'SRG';

    protected $table = 'tiket';

    protected $fillable = [
        'nomor_tiket',
        'pemohon_id',
        'detail_type',
        'detail_id',
        'status',
        'channel',
        'judul',
        'keterangan',
        'assigned_to',
        'selesai_at',
    ];

    protected $casts = [
        'status' => TiketStatus::class,
        'channel' => TiketChannel::class,
        'selesai_at' => 'datetime',
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class);
    }

    public function detail(): MorphTo
    {
        return $this->morphTo();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
