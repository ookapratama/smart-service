<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Pengaduan extends Model
{
    use LogsActivity;

    protected $table = 'pengaduan';

    protected $fillable = [
        'kategori_pengaduan_id',
        'deskripsi',
        'lokasi',
        'lat',
        'lng',
        'is_anonim',
        'jenis_laporan',
        'tanggal_kejadian',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'is_anonim' => 'boolean',
        'tanggal_kejadian' => 'date',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengaduan::class, 'kategori_pengaduan_id');
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
