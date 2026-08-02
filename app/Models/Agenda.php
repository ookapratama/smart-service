<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agenda extends Model
{
    use LogsActivity;

    protected $table = 'agenda';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'penyelenggara',
        'lokasi',
        'mulai_at',
        'selesai_at',
        'waktu_keterangan',
        'ringkasan',
        'deskripsi',
        'gambar',
        'file_lampiran',
        'status_agenda',
        'views',
        'is_published',
    ];

    protected $casts = [
        'mulai_at'     => 'datetime',
        'selesai_at'   => 'datetime',
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeMendatang($query)
    {
        return $query->where('is_published', true)
                     ->where('mulai_at', '>=', now())
                     ->orderBy('mulai_at', 'asc');
    }

    public function scopeSelesai($query)
    {
        return $query->where('is_published', true)
                     ->where('mulai_at', '<', now())
                     ->orderBy('mulai_at', 'desc');
    }
}
