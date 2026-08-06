<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemohon extends Model
{
    use LogsActivity;

    protected $table = 'pemohon';

    protected $fillable = [
        'user_id',
        'kelurahan_id',
        'nik',
        'name',
        'phone',
        'phone_verified_at',
        'email',
        'alamat',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function tikets(): HasMany
    {
        return $this->hasMany(Tiket::class);
    }

    public function provisionWargaUser(): User
    {
        $this->phone_verified_at ??= now();

        if ($this->user_id && $this->user) {
            $this->save();

            return $this->user;
        }

        $wargaRole = Role::firstOrCreate(
            ['slug' => 'warga'],
            ['name' => 'Warga']
        );

        $user = User::create([
            'name' => $this->name ?: 'Warga '.$this->nik,
            'email' => $this->email ?? null,
            'password' => null,
            'role_id' => $wargaRole->id,
        ]);

        $this->user_id = $user->id;
        $this->save();

        return $user;
    }
}
