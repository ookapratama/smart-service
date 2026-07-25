<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PemohonRequest extends BaseRequest
{
    public function rules(): array
    {
        $pemohonId = $this->route('pemohon');

        return [
            'instansi_id' => 'required|exists:instansi,id',
            'nik' => [
                'required',
                'string',
                'size:16',
                Rule::unique('pemohon', 'nik')
                    ->where('instansi_id', $this->input('instansi_id'))
                    ->ignore($pemohonId),
            ],
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'instansi_id.required' => 'Instansi wajib dipilih',
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar pada instansi ini',
            'name.required' => 'Nama pemohon wajib diisi',
        ];
    }
}
