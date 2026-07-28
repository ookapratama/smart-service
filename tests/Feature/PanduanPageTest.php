<?php

test('panduan page renders with all key sections', function () {
    $response = $this->get(route('panduan'));

    $response->assertOk();
    $response->assertSee('Panduan Layanan 3S');
    $response->assertSee('Cara Membuat Pengaduan');
    $response->assertSee('Cara Mengajukan Surat');
    $response->assertSee('Cara Cek Status Tiket');
    $response->assertSee('Login Warga');
    $response->assertSee('Segera Hadir');
    $response->assertSee('Pertanyaan yang Sering Diajukan');
});
