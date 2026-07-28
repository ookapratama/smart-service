<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratCounter extends Model
{
    protected $table = 'surat_counters';

    protected $fillable = [
        'periode',
        'last_seq',
    ];
}
