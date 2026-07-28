<?php

namespace App\Http\Requests;

class JadwalPelayananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'kelurahan_id' => 'required|exists:kelurahan,id',
            'hari' => 'required|array|min:1',
            'hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_buka' => 'required|string|max:10',
            'jam_tutup' => 'required|string|max:10',
            'istirahat' => 'nullable|string|max:50',
            'petugas' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih',
            'hari.required' => 'Pilih minimal satu hari operasional',
            'jam_buka.required' => 'Jam buka wajib diisi',
            'jam_tutup.required' => 'Jam tutup wajib diisi',
        ];
    }
}
