<?php

namespace App\Http\Requests;

class BeritaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'ringkasan' => 'nullable|string',
            'konten' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'penulis' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul berita wajib diisi',
            'kategori.required' => 'Kategori berita wajib diisi',
            'gambar.image' => 'File gambar harus berupa format gambar valid',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
