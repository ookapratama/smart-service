<?php

namespace App\Http\Requests;

class GaleriRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'judul'        => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'tipe'         => 'nullable|in:foto,video',
            'video_url'    => 'nullable|url|max:255',
            'tgl_kegiatan' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'    => 'Judul galeri wajib diisi',
            'kategori.required' => 'Kategori galeri wajib diisi',
            'gambar.image'      => 'File gambar harus berupa format gambar valid',
            'gambar.max'        => 'Ukuran gambar maksimal 3MB',
            'video_url.url'     => 'URL video harus format URL YouTube / Vimeo valid',
        ];
    }
}
