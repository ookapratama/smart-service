<?php

namespace App\Http\Requests;

class JadwalPelayananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'kelurahan' => 'required|string|max:100',
            'jam_buka' => 'required|string|max:10',
            'jam_tutup' => 'required|string|max:10',
            'istirahat' => 'nullable|string|max:50',
            'hari_operasional' => 'nullable|string|max:100',
            'petugas' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan.required' => 'Nama kelurahan wajib diisi',
            'jam_buka.required' => 'Jam buka wajib diisi',
            'jam_tutup.required' => 'Jam tutup wajib diisi',
        ];
    }
}
