<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiketCounter extends Model
{
    protected $table = 'tiket_counters';

    protected $fillable = [
        'instansi_id',
        'periode',
        'last_seq',
    ];

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }
}
