<?php

namespace App\Http\Requests;

class JenisSuratRequest extends BaseRequest
{
    public function rules(): array
    {
        $jenisSuratId = $this->route('jenis_surat');

        // wajib_pengantar_rt_rw sengaja TIDAK ada di rules: kolom itu
        // deprecated — lampiran pengantar dimodelkan sebagai field
        // {type: file} di dalam `fields` (S3_MVP_DESIGN.md §6), bukan kolom.
        return [
            'kode' => 'required|string|max:20|unique:jenis_surat,kode,'.$jenisSuratId,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'template_view' => 'nullable|in:keterangan,pengantar,skd,generik',
            'fields' => 'nullable|array',
            'fields.*.name' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/', 'distinct'],
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,number,date,select,file',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.options' => 'required_if:fields.*.type,select|nullable|array|min:1',
            'fields.*.options.*' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode jenis surat wajib diisi',
            'kode.unique' => 'Kode jenis surat sudah digunakan',
            'nama.required' => 'Nama jenis surat wajib diisi',
            'fields.*.name.required' => 'Nama field wajib diisi',
            'fields.*.name.regex' => 'Nama field harus huruf kecil/angka/underscore dan diawali huruf (contoh: alamat_domisili)',
            'fields.*.name.distinct' => 'Nama field tidak boleh duplikat',
            'fields.*.label.required' => 'Label field wajib diisi',
            'fields.*.type.in' => 'Tipe field tidak dikenal',
            'fields.*.options.required_if' => 'Field bertipe pilihan wajib punya minimal satu opsi',
        ];
    }

    /**
     * Normalisasi input builder: buang baris kosong, cast checkbox required,
     * dan pecah textarea options (satu opsi per baris) menjadi array.
     */
    protected function prepareForValidation(): void
    {
        $fields = collect($this->input('fields', []))
            ->filter(fn ($field) => is_array($field)
                && (($field['name'] ?? '') !== '' || ($field['label'] ?? '') !== ''))
            ->map(function (array $field) {
                $field['required'] = filter_var($field['required'] ?? false, FILTER_VALIDATE_BOOL);
                $field['options'] = $this->normalizeOptions($field);

                if ($field['options'] === null) {
                    unset($field['options']);
                }

                return $field;
            })
            ->values()
            ->all();

        $this->merge([
            'fields' => $fields,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeOptions(array $field): ?array
    {
        if (($field['type'] ?? '') !== 'select') {
            return null;
        }

        $options = $field['options'] ?? [];

        if (is_string($options)) {
            $options = preg_split('/\r\n|\r|\n/', $options) ?: [];
        }

        return collect($options)
            ->map(fn ($option) => trim((string) $option))
            ->filter()
            ->values()
            ->all();
    }
}
