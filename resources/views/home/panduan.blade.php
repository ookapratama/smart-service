@extends('home.layouts.app')

@section('title', 'Panduan Layanan - Soreang Smart Service (3S)')
@section('meta_description', 'Panduan langkah demi langkah menggunakan layanan online Kecamatan Soreang: membuat pengaduan, mengajukan surat, dan cek status tiket.')

@section('content')

  <style>
    #panduan-pengaduan, #panduan-surat, #panduan-cek-status, #panduan-login, #panduan-faq {
      scroll-margin-top: 110px;
    }
    .panduan-steps {
      list-style: none;
      counter-reset: panduan-step;
      padding-left: 0;
      margin-bottom: 1.25rem;
    }
    .panduan-steps > li {
      position: relative;
      counter-increment: panduan-step;
      padding-left: 2.75rem;
      margin-bottom: 0.9rem;
      line-height: 1.6;
      min-height: 2rem;
    }
    .panduan-steps > li::before {
      content: counter(panduan-step);
      position: absolute;
      left: 0;
      top: 0;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      background-color: var(--bs-primary, #106eea);
      color: #fff;
      font-weight: 700;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .quick-jump-card {
      min-width: 150px;
      scroll-snap-align: start;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .quick-jump-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    @media (min-width: 768px) {
      .quick-jump-card {
        min-width: 0;
      }
    }
  </style>

  <!-- HERO PANDUAN -->
  <section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(8, 35, 95, 0.92) 0%, rgba(4, 18, 55, 0.95) 100%); min-height: 36vh; display: flex; align-items: center;">
    <div class="container py-4 position-relative z-2">
      <div class="row justify-content-center text-center">
        <div class="col-lg-9" data-aos="fade-up">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3 bg-white bg-opacity-20 border border-white border-opacity-30 shadow-sm mx-auto">
            <i class="bi bi-book text-dark"></i>
            <span class="small fw-semibold text-dark">Pusat Bantuan Warga</span>
          </div>
          <h1 class="display-5 fw-bold mb-3 text-white">Panduan Layanan 3S</h1>
          <p class="fs-5 mb-0 px-lg-5" style="color: #f1f5f9;">
            Semua layanan bisa Anda gunakan dari HP, tanpa perlu datang ke kantor dan tanpa harus punya akun.
            Ikuti langkah-langkah sederhana di bawah ini.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5 bg-light">
    <div class="container py-3" style="max-width: 980px;">

      <!-- QUICK JUMP -->
      <div class="d-flex d-md-grid overflow-x-auto overflow-md-visible flex-nowrap gap-3 pb-2 mb-5" style="grid-template-columns: repeat(4, 1fr); scroll-snap-type: x mandatory;">
        <a href="#panduan-pengaduan" class="quick-jump-card text-decoration-none flex-shrink-0" data-aos="fade-up" data-aos-delay="100">
          <div class="d-flex flex-column align-items-center text-center p-3 bg-white rounded-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-2" style="width: 48px; height: 48px; font-size: 1.3rem;">
              <i class="bi bi-megaphone"></i>
            </div>
            <span class="fw-semibold small text-dark">Pengaduan</span>
          </div>
        </a>
        <a href="#panduan-surat" class="quick-jump-card text-decoration-none flex-shrink-0" data-aos="fade-up" data-aos-delay="200">
          <div class="d-flex flex-column align-items-center text-center p-3 bg-white rounded-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-2" style="width: 48px; height: 48px; font-size: 1.3rem;">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <span class="fw-semibold small text-dark">Buat Surat</span>
          </div>
        </a>
        <a href="#panduan-cek-status" class="quick-jump-card text-decoration-none flex-shrink-0" data-aos="fade-up" data-aos-delay="300">
          <div class="d-flex flex-column align-items-center text-center p-3 bg-white rounded-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-2" style="width: 48px; height: 48px; font-size: 1.3rem;">
              <i class="bi bi-search"></i>
            </div>
            <span class="fw-semibold small text-dark">Cek Status</span>
          </div>
        </a>
        <a href="#panduan-login" class="quick-jump-card text-decoration-none flex-shrink-0" data-aos="fade-up" data-aos-delay="400">
          <div class="d-flex flex-column align-items-center text-center p-3 bg-white rounded-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-2" style="width: 48px; height: 48px; font-size: 1.3rem;">
              <i class="bi bi-person-circle"></i>
            </div>
            <span class="fw-semibold small text-dark">Login Warga</span>
          </div>
        </a>
      </div>

      <!-- A. CARA MEMBUAT PENGADUAN -->
      <div id="panduan-pengaduan" class="p-4 p-md-5 bg-white rounded-4 shadow-sm border mb-4 overflow-hidden" data-aos="fade-up">
        <div class="row align-items-center g-4 g-lg-5">
          <div class="col-lg-5 text-center">
            <img src="{{ asset('assets/img/illustrations/girl-with-laptop-light.png') }}" alt="Ilustrasi warga melaporkan pengaduan secara online lewat HP" class="img-fluid" style="max-width: 280px;" loading="lazy">
          </div>
          <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                <i class="bi bi-megaphone"></i>
              </div>
              <h2 class="fs-4 fw-bold text-dark mb-0">Cara Membuat Pengaduan</h2>
            </div>
            <p class="text-muted">Ada jalan rusak, lampu mati, atau keluhan pelayanan? Laporkan langsung — <strong>tidak perlu akun dan tidak perlu kode apa pun</strong>.</p>
            <ol class="panduan-steps text-dark">
              <li>Buka menu <a href="{{ route('pengaduan.index') }}" class="fw-semibold">Pengaduan</a>, lalu tekan tombol <strong>Buat Pengaduan Baru</strong>.</li>
              <li>Isi data diri Anda: NIK (16 digit di KTP), nama, nomor WhatsApp, dan kelurahan.</li>
              <li>Pilih kategori laporan, tulis judul dan ceritakan kejadiannya (lokasi dan tanggal kejadian membantu petugas).</li>
              <li>Jika ada, lampirkan foto kejadian agar laporan lebih jelas.</li>
              <li>Tekan <strong>Kirim</strong>. Anda langsung mendapat <strong>nomor tiket</strong> di layar dan dikirim juga ke WhatsApp Anda.</li>
            </ol>
            <div class="alert alert-light border rounded-3 small mb-0">
              <i class="bi bi-lightbulb text-warning me-1"></i>
              Identitas Anda bisa disembunyikan dari tampilan petugas dengan mencentang pilihan <em>anonim</em> saat mengisi form.
            </div>
          </div>
        </div>
      </div>

      <!-- B. CARA MENGAJUKAN SURAT -->
      <div id="panduan-surat" class="p-4 p-md-5 bg-white rounded-4 shadow-sm border mb-4 overflow-hidden" data-aos="fade-up">
        <div class="row align-items-center g-4 g-lg-5">
          <div class="col-lg-5 order-lg-2 text-center">
            <img src="{{ asset('assets/img/illustrations/auth-register-multi-steps-illustration.png') }}" alt="Ilustrasi tahapan mengisi formulir pengajuan surat secara online" class="img-fluid mb-3" style="max-width: 280px;" loading="lazy">
            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3 border text-start">
              <img src="{{ asset('assets/img/illustrations/auth-two-steps-illustration-light.png') }}" alt="Ilustrasi verifikasi kode WhatsApp saat mengajukan surat" class="img-fluid flex-shrink-0" style="max-width: 90px;" loading="lazy">
              <span class="small text-muted">Nomor WhatsApp Anda diverifikasi dengan kode 6 digit sebelum surat dikirim.</span>
            </div>
          </div>
          <div class="col-lg-7 order-lg-1">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <h2 class="fs-4 fw-bold text-dark mb-0">Cara Mengajukan Surat</h2>
            </div>
            <p class="text-muted">Surat keterangan (domisili, tidak mampu, usaha, dan lainnya) bisa diurus online. Karena surat adalah dokumen resmi atas nama Anda, sistem akan <strong>memastikan nomor WhatsApp benar milik Anda</strong> lewat kode verifikasi.</p>
            <ol class="panduan-steps text-dark">
              <li>Buka menu <a href="{{ route('surat.index') }}" class="fw-semibold">Buat Surat</a>, lalu pilih jenis surat yang Anda butuhkan.</li>
              <li>Isi data diri: NIK, nama, nomor WhatsApp aktif, kelurahan, dan alamat.</li>
              <li>Isi data tambahan sesuai jenis surat (misalnya alamat domisili atau nama usaha).</li>
              <li>Jika jenis surat mensyaratkan lampiran (misalnya <strong>surat pengantar RT/RW</strong>), unggah foto atau hasil scan-nya di form yang sama.</li>
              <li>Tekan tombol <strong>Kirim Kode WhatsApp</strong>. Sistem mengirim <strong>kode 6 digit</strong> ke nomor WhatsApp Anda.</li>
              <li>Masukkan kode tersebut ke form (berlaku 5 menit; jika hangus, minta kode baru).</li>
              <li>Setelah kode cocok, tekan <strong>Kirim Pengajuan</strong>. Nomor tiket muncul di layar dan dikirim ke WhatsApp Anda.</li>
              <li>Petugas memeriksa berkas Anda. Jika disetujui, surat resmi ber-nomor diterbitkan — pantau lewat halaman <strong>Cek Status</strong>: saat status <strong>Selesai</strong>, surat siap diunduh.</li>
            </ol>
            <div class="alert alert-light border rounded-3 small mb-0">
              <i class="bi bi-lightbulb text-warning me-1"></i>
              Untuk mengunduh surat yang sudah jadi, buka halaman Cek Status atau tautan di WhatsApp Anda, lalu masukkan nomor tiket dan NIK.
            </div>
          </div>
        </div>
      </div>

      <!-- C. CARA CEK STATUS TIKET -->
      <div id="panduan-cek-status" class="p-4 p-md-5 bg-white rounded-4 shadow-sm border mb-4 overflow-hidden" data-aos="fade-up">
        <div class="row align-items-center g-4 g-lg-5">
          <div class="col-lg-5 text-center">
            <div class="mx-auto p-4 rounded-4 border border-2 border-primary-subtle bg-primary bg-opacity-10" style="max-width: 280px;">
              <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-search text-primary fs-4"></i>
                <span class="fw-semibold text-primary small">Lacak Tiket Anda</span>
              </div>
              <div class="bg-white rounded-3 border p-3 mb-2 text-center">
                <div class="text-muted small mb-1">Nomor Tiket</div>
                <div class="fw-bold text-dark font-monospace">SRG-2607-00123</div>
              </div>
              <div class="d-flex align-items-center justify-content-between bg-white rounded-3 border p-3">
                <span class="small text-muted">Status</span>
                <span class="badge bg-success">Selesai</span>
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                <i class="bi bi-search"></i>
              </div>
              <h2 class="fs-4 fw-bold text-dark mb-0">Cara Cek Status Tiket</h2>
            </div>
            <p class="text-muted">Setiap pengaduan dan pengajuan surat punya <strong>nomor tiket</strong> (contoh: <code>SRG-2607-00123</code>). Dengan nomor itu Anda bisa memantau prosesnya kapan saja.</p>
            <ol class="panduan-steps text-dark">
              <li>Buka menu <a href="{{ route('cek-status.index') }}" class="fw-semibold">Cek Status</a>.</li>
              <li>Masukkan <strong>nomor tiket</strong> Anda (tersimpan juga di pesan WhatsApp yang Anda terima) atau <strong>NIK</strong>.</li>
              <li>Tekan <strong>Cari Tiket</strong> — status terbaru dan catatan petugas langsung ditampilkan.</li>
            </ol>
            <div class="alert alert-light border rounded-3 small mb-0">
              <i class="bi bi-shield-check text-success me-1"></i>
              Untuk mengunduh surat yang sudah terbit, sistem meminta <strong>NIK pemohon</strong> sebagai pengaman — hanya pemilik tiket yang bisa mengunduh suratnya.
            </div>
          </div>
        </div>
      </div>

      <!-- D. LOGIN WARGA -->
      <div id="panduan-login" class="p-4 p-md-5 bg-white rounded-4 shadow-sm border mb-4 overflow-hidden" data-aos="fade-up">
        <div class="row align-items-center g-4 g-lg-5">
          <div class="col-lg-5 order-lg-2 text-center">
            <img src="{{ asset('assets/img/illustrations/auth-two-steps-illustration-light.png') }}" alt="Ilustrasi login warga dengan NIK dan kode WhatsApp" class="img-fluid" style="max-width: 280px;" loading="lazy">
          </div>
          <div class="col-lg-7 order-lg-1">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                <i class="bi bi-person-circle"></i>
              </div>
              <h2 class="fs-4 fw-bold text-dark mb-0">Login Warga</h2>
            </div>
            <p class="text-muted mb-2">
              Anda bisa <strong>masuk (login) cukup dengan NIK</strong> — tanpa password:
            </p>
            <ol class="panduan-steps text-dark">
              <li>Buka halaman <a href="{{ route('warga.login') }}" class="fw-semibold">Masuk Warga</a>.</li>
              <li>Masukkan <strong>NIK 16 digit</strong> Anda, lalu tekan <strong>Kirim Kode OTP</strong>.</li>
              <li>Masukkan <strong>kode WhatsApp 6 digit</strong> yang dikirim ke nomor Anda yang terdaftar — Anda langsung masuk.</li>
            </ol>
            <p class="text-muted mb-2">Setelah login Anda dapat melihat <strong>seluruh riwayat tiket</strong> (pengaduan dan surat) di halaman <strong>Tiket Saya</strong> serta mengunduh surat-surat yang sudah terbit — tanpa mengetik nomor tiket satu per satu.</p>
            <p class="text-muted mb-0"><strong>Tidak ada pendaftaran akun.</strong> Cukup sekali mengajukan surat atau membuat pengaduan, data Anda otomatis dikenali saat pertama kali login.</p>
          </div>
        </div>
      </div>

      <!-- E. FAQ SINGKAT -->
      <div id="panduan-faq" class="p-4 p-md-5 bg-white rounded-4 shadow-sm border" data-aos="fade-up">
        <div class="row align-items-center g-3 mb-4">
          <div class="col-auto">
            <img src="{{ asset('assets/img/illustrations/faq-illustration.png') }}" alt="Ilustrasi pertanyaan yang sering diajukan warga" style="width: 64px; height: 64px; object-fit: contain;" loading="lazy">
          </div>
          <div class="col">
            <h2 class="fs-4 fw-bold text-dark mb-0">Pertanyaan yang Sering Diajukan</h2>
          </div>
        </div>

        <div class="accordion" id="accordionPanduanFaq">
          <div class="accordion-item border rounded-3 mb-2">
            <h3 class="accordion-header">
              <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqAkun">
                Apakah saya harus punya akun untuk menggunakan layanan ini?
              </button>
            </h3>
            <div id="faqAkun" class="accordion-collapse collapse show" data-bs-parent="#accordionPanduanFaq">
              <div class="accordion-body text-muted">
                Tidak. Membuat pengaduan, mengajukan surat, dan cek status semuanya bisa tanpa akun.
                Cukup isi data diri di form — sistem mengenali Anda dari NIK, sehingga semua tiket Anda otomatis tersambung menjadi satu riwayat.
              </div>
            </div>
          </div>

          <div class="accordion-item border rounded-3 mb-2">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqGantiWa">
                Bagaimana jika nomor WhatsApp saya berganti?
              </button>
            </h3>
            <div id="faqGantiWa" class="accordion-collapse collapse" data-bs-parent="#accordionPanduanFaq">
              <div class="accordion-body text-muted">
                Demi keamanan, jika NIK Anda sudah pernah terverifikasi dengan nomor lama, kode verifikasi dikirim ke <strong>nomor lama</strong> terlebih dahulu.
                Jika kode itu berhasil dimasukkan, nomor baru langsung menggantikannya. Jika nomor lama sudah tidak aktif,
                silakan datang atau hubungi petugas kecamatan untuk verifikasi manual.
              </div>
            </div>
          </div>

          <div class="accordion-item border rounded-3 mb-2">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqKode">
                Kode WhatsApp tidak masuk atau kadaluarsa, bagaimana?
              </button>
            </h3>
            <div id="faqKode" class="accordion-collapse collapse" data-bs-parent="#accordionPanduanFaq">
              <div class="accordion-body text-muted">
                Kode berlaku 5 menit dan bisa diminta ulang setelah menunggu sekitar 60 detik.
                Pastikan nomor WhatsApp yang Anda tulis aktif dan bisa menerima pesan. Salah memasukkan kode berkali-kali akan mengunci kode tersebut — cukup minta kode baru.
              </div>
            </div>
          </div>

          <div class="accordion-item border rounded-3 mb-2">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqBiaya">
                Apakah layanan ini berbayar?
              </button>
            </h3>
            <div id="faqBiaya" class="accordion-collapse collapse" data-bs-parent="#accordionPanduanFaq">
              <div class="accordion-body text-muted">
                Tidak. Seluruh layanan Kecamatan Soreang melalui 3S — pengaduan, pengajuan surat, dan cek status — sepenuhnya gratis.
              </div>
            </div>
          </div>

          <div class="accordion-item border rounded-3">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqDitolak">
                Kenapa pengajuan surat saya bisa ditolak?
              </button>
            </h3>
            <div id="faqDitolak" class="accordion-collapse collapse" data-bs-parent="#accordionPanduanFaq">
              <div class="accordion-body text-muted">
                Umumnya karena berkas belum lengkap atau data tidak sesuai (misalnya lampiran pengantar RT/RW tidak terbaca).
                Alasan penolakan ditulis petugas dan bisa Anda lihat di Cek Status — perbaiki, lalu ajukan kembali kapan saja.
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

@endsection
