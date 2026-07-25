<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    use LogsActivity;

    protected $table = 'jenis_surat';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'fields',
        'wajib_pengantar_rt_rw',
        'is_active',
    ];

    protected $casts = [
        'fields' => 'array',
        'wajib_pengantar_rt_rw' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function pengajuanSurat(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
