<?php

namespace App\Http\Requests;

class KategoriPengaduanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi',
        ];
    }
}
