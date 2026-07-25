<?php

namespace App\Models;

use App\Enums\WilayahLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Data referensi wilayah administratif Indonesia (provinsi/kab-kota/kecamatan),
 * disinkronkan dari wilayah.id via `php artisan wilayah:sync`. Read-only lookup
 * untuk dropdown — bukan tabel tenant (lihat Instansi).
 */
class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'parent_code',
        'name',
        'level',
    ];

    protected $casts = [
        'level' => WilayahLevel::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'parent_code', 'code');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Wilayah::class, 'parent_code', 'code');
    }

    public function scopeProvinsi($query)
    {
        return $query->where('level', WilayahLevel::Provinsi);
    }

    public function scopeKabupatenKota($query)
    {
        return $query->where('level', WilayahLevel::KabupatenKota);
    }

    public function scopeKecamatan($query)
    {
        return $query->where('level', WilayahLevel::Kecamatan);
    }
}
