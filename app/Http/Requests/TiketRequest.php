<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Validasi transisi status tiket. Tiket tidak memiliki form create/edit
 * generik — nomor_tiket & detail (morph) dibuat lewat alur pengajuan modul
 * masing-masing (fase berikutnya), bukan dari halaman admin ini.
 */
class TiketRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status_to' => ['required', Rule::in(['diproses', 'selesai', 'ditolak'])],
            // Penolakan tanpa alasan = warga menerima kabar buruk tanpa penjelasan.
            'catatan' => 'required_if:status_to,ditolak|nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status_to.required' => 'Status tujuan wajib dipilih',
            'status_to.in' => 'Status tujuan tidak valid',
            'catatan.required_if' => 'Catatan wajib diisi saat menolak tiket — jelaskan alasannya untuk pemohon.',
        ];
    }
}
