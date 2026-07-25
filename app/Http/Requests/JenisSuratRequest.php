<?php

namespace App\Http\Requests;

class JenisSuratRequest extends BaseRequest
{
    public function rules(): array
    {
        $jenisSuratId = $this->route('jenis_surat');

        return [
            'kode' => 'required|string|max:20|unique:jenis_surat,kode,'.$jenisSuratId,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'wajib_pengantar_rt_rw' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode jenis surat wajib diisi',
            'kode.unique' => 'Kode jenis surat sudah digunakan',
            'nama.required' => 'Nama jenis surat wajib diisi',
        ];
    }
}
