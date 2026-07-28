<?php

namespace App\Services;

use App\Repositories\PemohonRepository;

class PemohonService extends BaseService
{
    public function __construct(PemohonRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Petugas mengoreksi nomor WA pemohon (mis. warga lapor nomor lama tak
     * aktif) — perubahan manual ini tidak boleh mewarisi status "terverifikasi"
     * milik nomor lama. Reset phone_verified_at supaya nomor baru wajib
     * dibuktikan lagi lewat OTP saat pemohon berikutnya kali memakai form
     * publik (§4 S3_MVP_DESIGN.md).
     */
    public function update(int $id, array $data)
    {
        if (array_key_exists('phone', $data)) {
            $current = $this->repository->find($id);

            if ($current && $current->phone !== $data['phone']) {
                $data['phone_verified_at'] = null;
            }
        }

        return parent::update($id, $data);
    }
}
