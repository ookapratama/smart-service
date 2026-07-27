<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'ringkasan',
        'isi',
        'thumbnail',
        'penulis',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published'  => 'boolean',
    ];

    /**
     * Scope: hanya berita yang sudah dipublish.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Accessor alias for gambar -> thumbnail
     */
    public function getGambarAttribute()
    {
        return $this->attributes['thumbnail'] ?? null;
    }

    /**
     * Mutator alias for gambar -> thumbnail
     */
    public function setGambarAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
    }

    /**
     * Accessor alias for konten -> isi
     */
    public function getKontenAttribute()
    {
        return $this->attributes['isi'] ?? null;
    }

    /**
     * Mutator alias for konten -> isi
     */
    public function setKontenAttribute($value)
    {
        $this->attributes['isi'] = $value;
    }
}
