@extends('home.layouts.app')

@section('title', 'Laporan Berhasil Dikirim - Sorean Smart Service (3S)')

@section('content')

  <section class="section light-background py-5" style="background-color: #f4f6f9; min-height: 80vh;">
    <div class="container py-4" style="max-width: 680px;" data-aos="fade-up">

      <div class="card border-0 shadow-lg rounded-4 text-center overflow-hidden">
        
        <!-- Header Banner Sukses -->
        <div class="bg-success text-white p-4 p-md-5">
          <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px;">
            <i class="bi bi-check-lg display-4 fw-bold"></i>
          </div>
          <h2 class="fw-bold text-white mb-2">Laporan Pengaduan Berhasil Dikirim!</h2>
          <p class="text-white-50 mb-0">Terima kasih. Pengaduan Anda telah tercatat dan diteruskan ke tim penanganan 3S Sorean.</p>
        </div>

        <div class="card-body p-4 p-md-5">

          <!-- Ticket Box Card -->
          <div class="p-4 bg-light rounded-4 border border-2 border-primary-subtle mb-4 text-start">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-ticket-perforated me-1"></i> NOMOR TIKET RESMI
              </span>
              <span class="badge bg-warning text-dark px-3 py-2 rounded-pill text-uppercase">BARU / TERDAFTAR</span>
            </div>

            <div class="text-center py-2 my-2 bg-white rounded-3 border">
              <h1 class="fw-bold text-primary font-monospace display-6 mb-0" id="s3NomorTiketText">{{ $tiket->nomor_tiket }}</h1>
            </div>

            <div class="row g-2 text-sm mt-3 pt-3 border-top">
              <div class="col-6"><span class="text-muted">Pelapor:</span><br><strong class="text-dark">{{ $tiket->pemohon ? $tiket->pemohon->name : 'Anonim' }}</strong></div>
              <div class="col-6"><span class="text-muted">Tanggal Dikirim:</span><br><strong class="text-dark">{{ $tiket->created_at->format('d M Y H:i') }} WIB</strong></div>
              <div class="col-12 mt-2"><span class="text-muted">Judul Pengaduan:</span><br><strong class="text-dark">{{ $tiket->judul }}</strong></div>
            </div>
          </div>

          <div class="alert alert-warning border-0 rounded-3 mb-4 text-start d-flex align-items-start">
            <i class="bi bi-bookmark-star-fill fs-4 me-3 text-warning flex-shrink-0"></i>
            <div>
              <strong>Simpan Nomor Tiket Anda!</strong><br>
              <small>Gunakan nomor tiket <code>{{ $tiket->nomor_tiket }}</code> di atas untuk mengecek progres live tindakan penanganan petugas pada fitur <strong>Cek Status Tiket</strong>.</small>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg rounded-3 fw-bold py-3" onclick="copyTiketCode('{{ $tiket->nomor_tiket }}')">
              <i class="bi bi-clipboard-check me-2"></i> Salin Kode Nomor Tiket
            </button>
            <a href="{{ route('home') }}#cek-status" class="btn btn-outline-primary btn-lg rounded-3 py-2">
              <i class="bi bi-search me-1"></i> Cek Progres Status Tiket
            </a>
            <a href="{{ route('pengaduan.create') }}" class="btn btn-link text-muted mt-2">
              Kirim Pengaduan Lain
            </a>
          </div>

        </div>
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  function copyTiketCode(code) {
    navigator.clipboard.writeText(code).then(function() {
      alert('Nomor tiket [' + code + '] berhasil disalin ke clipboard!');
    }).catch(function() {
      alert('Nomor tiket: ' + code);
    });
  }
</script>
@endpush
