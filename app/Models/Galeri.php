<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Galeri extends Model
{
    use LogsActivity;

    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'keterangan',
        'gambar',
        'tipe',
        'video_url',
        'tgl_kegiatan',
        'views',
        'is_published',
    ];

    protected $casts = [
        'tgl_kegiatan' => 'date',
        'is_published' => 'boolean',
        'views'        => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
        });
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
