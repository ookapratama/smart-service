<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PemohonRequest extends BaseRequest
{
    public function rules(): array
    {
        $pemohonId = $this->route('pemohon');

        return [
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('pemohon', 'nik')->ignore($pemohonId),
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
            'nik.required' => 'NIK wajib diisi',
            'nik.digits' => 'NIK harus 16 digit angka',
            'nik.unique' => 'NIK sudah terdaftar',
            'name.required' => 'Nama pemohon wajib diisi',
        ];
    }
}
