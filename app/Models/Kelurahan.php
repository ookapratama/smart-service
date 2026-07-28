<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    use LogsActivity;

    protected $table = 'kelurahan';

    protected $fillable = [
        'nama',
        'kode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pemohon(): HasMany
    {
        return $this->hasMany(Pemohon::class);
    }

    public function jadwalPelayanan(): HasMany
    {
        return $this->hasMany(JadwalPelayanan::class);
    }
}
