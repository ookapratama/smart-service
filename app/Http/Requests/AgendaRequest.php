<?php

namespace App\Http\Requests;

class AgendaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'judul'            => 'required|string|max:255',
            'kategori'         => 'required|string|max:100',
            'penyelenggara'    => 'nullable|string|max:255',
            'lokasi'           => 'nullable|string|max:255',
            'mulai_at'         => 'required|date',
            'selesai_at'       => 'nullable|date|after_or_equal:mulai_at',
            'waktu_keterangan' => 'nullable|string|max:100',
            'ringkasan'        => 'nullable|string|max:500',
            'deskripsi'        => 'nullable|string',
            'gambar'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'file_lampiran'    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:5120',
            'status_agenda'    => 'nullable|in:mendatang,berlangsung,selesai',
            'is_published'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'      => 'Judul agenda kegiatan wajib diisi',
            'kategori.required'   => 'Kategori agenda wajib diisi',
            'mulai_at.required'   => 'Waktu mulai agenda wajib diisi',
            'selesai_at.after_or_equal' => 'Waktu selesai harus setelah atau sama dengan waktu mulai',
            'gambar.image'        => 'File poster harus berupa gambar valid',
            'file_lampiran.mimes' => 'Format file lampiran harus PDF, Office, atau ZIP',
        ];
    }
}
