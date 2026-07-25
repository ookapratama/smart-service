<?php

namespace App\Models;

use App\Traits\BelongsToInstansi;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemohon extends Model
{
    use BelongsToInstansi;
    use LogsActivity;

    protected $table = 'pemohon';

    protected $fillable = [
        'instansi_id',
        'user_id',
        'nik',
        'name',
        'phone',
        'email',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tikets(): HasMany
    {
        return $this->hasMany(Tiket::class);
    }
}
