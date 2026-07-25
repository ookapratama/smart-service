<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PengajuanSurat extends Model
{
    use LogsActivity;

    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'jenis_surat_id',
        'keperluan',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function tiket(): MorphOne
    {
        return $this->morphOne(Tiket::class, 'detail');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
