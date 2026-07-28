<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiketCounter extends Model
{
    protected $table = 'tiket_counters';

    protected $fillable = [
        'periode',
        'last_seq',
    ];
}
