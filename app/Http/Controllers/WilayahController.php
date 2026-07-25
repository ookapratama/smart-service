<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;

/**
 * Endpoint pendukung untuk cascading dropdown wilayah (provinsi/kab-kota/kecamatan).
 * Data referensi read-only, tidak terikat menu/permission tersendiri — dipakai
 * di dalam halaman yang sudah dilindungi (lihat form Instansi).
 */
class WilayahController extends Controller
{
    /**
     * Daftar anak wilayah dari sebuah kode. Gunakan 'root' untuk daftar provinsi.
     */
    public function children(string $parentCode = 'root'): JsonResponse
    {
        $query = $parentCode === 'root'
            ? Wilayah::provinsi()
            : Wilayah::where('parent_code', $parentCode);

        return response()->json(
            $query->orderBy('name')->get(['code', 'name'])
        );
    }
}
