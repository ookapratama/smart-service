<?php

namespace App\Models;

use App\Enums\InstansiLevel;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    use LogsActivity;

    protected $table = 'instansi';

    protected $fillable = [
        'parent_id',
        'name',
        'level',
        'kode',
        'subdomain',
        'alamat',
        'telepon',
        'is_active',
    ];

    protected $casts = [
        'level' => InstansiLevel::class,
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Instansi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Instansi::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pemohons(): HasMany
    {
        return $this->hasMany(Pemohon::class);
    }

    public function tikets(): HasMany
    {
        return $this->hasMany(Tiket::class);
    }
}
