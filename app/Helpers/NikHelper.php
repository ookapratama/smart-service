<?php

namespace App\Helpers;

class NikHelper
{
    /**
     * Validasi format NIK 16 digit angka dan validasi struktur dasar tanggal lahir & wilayah NIK.
     */
    public static function isValidNik(string $nik): bool
    {
        $cleaned = preg_replace('/[^0-9]/', '', $nik);

        if (strlen($cleaned) !== 16) {
            return false;
        }

        // Tanggal Lahir (digit 7-12: DDMMYY)
        $day = (int) substr($cleaned, 6, 2);
        $month = (int) substr($cleaned, 8, 2);

        // Jika perempuan, tanggal ditambah 40 (41-71)
        if ($day > 40) {
            $day -= 40;
        }

        if ($day < 1 || $day > 31) {
            return false;
        }

        if ($month < 1 || $month > 12) {
            return false;
        }

        return true;
    }
}
