<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengaduan extends Model
{
    use LogsActivity;

    protected $table = 'kategori_pengaduan';

    protected $fillable = [
        'nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
