<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class InstansiRequest extends BaseRequest
{
    public function rules(): array
    {
        $instansiId = $this->route('instansi');

        return [
            'parent_id' => 'nullable|exists:instansi,id',
            'name' => 'required|string|max:255',
            'level' => ['required', Rule::in(['kabupaten', 'kecamatan', 'kelurahan'])],
            'kode' => 'required|string|max:10|unique:instansi,kode,'.$instansiId,
            'subdomain' => 'nullable|string|max:255|unique:instansi,subdomain,'.$instansiId,
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama instansi wajib diisi',
            'level.required' => 'Tingkat instansi wajib dipilih',
            'level.in' => 'Tingkat instansi tidak valid',
            'kode.required' => 'Kode wilayah wajib diisi',
            'kode.unique' => 'Kode wilayah sudah digunakan',
            'subdomain.unique' => 'Subdomain sudah digunakan',
        ];
    }
}
