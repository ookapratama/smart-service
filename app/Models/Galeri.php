<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Galeri extends Model
{
    use LogsActivity;

    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'keterangan',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
