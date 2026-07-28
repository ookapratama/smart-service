@extends('home.layouts.app')

@section('title', 'Pengajuan Surat Berhasil Dikirim - Soreang Smart Service (3S)')

@section('content')

  <section class="section light-background py-5" style="background-color: #f4f6f9; min-height: 80vh;">
    <div class="container py-4" style="max-width: 680px;" data-aos="fade-up">

      <div class="card border-0 shadow-lg rounded-4 text-center overflow-hidden">

        <!-- Header Banner Sukses -->
        <div class="bg-success text-white p-4 p-md-5">
          <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px;">
            <i class="bi bi-check-lg display-4 fw-bold"></i>
          </div>
          <h2 class="fw-bold text-white mb-2">Pengajuan Surat Berhasil Dikirim!</h2>
          <p class="text-white-50 mb-0">Terima kasih. Pengajuan Anda telah tercatat dan menunggu verifikasi berkas oleh petugas Kecamatan Soreang.</p>
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
              <div class="col-6"><span class="text-muted">Pemohon:</span><br><strong class="text-dark">{{ $tiket->pemohon ? $tiket->pemohon->name : '-' }}</strong></div>
              <div class="col-6"><span class="text-muted">Tanggal Dikirim:</span><br><strong class="text-dark">{{ $tiket->created_at->format('d M Y H:i') }} WIB</strong></div>
              <div class="col-12 mt-2"><span class="text-muted">Jenis Surat:</span><br><strong class="text-dark">{{ $tiket->detail?->jenisSurat?->nama ?? $tiket->judul }}</strong></div>
            </div>
          </div>

          <div class="alert alert-warning border-0 rounded-3 mb-4 text-start d-flex align-items-start">
            <i class="bi bi-bookmark-star-fill fs-4 me-3 text-warning flex-shrink-0"></i>
            <div>
              <strong>Simpan Nomor Tiket Anda!</strong><br>
              <small>Nomor tiket <code>{{ $tiket->nomor_tiket }}</code> juga dikirim ke WhatsApp Anda. Gunakan nomor tersebut pada fitur <strong>Cek Status Tiket</strong> untuk memantau proses penerbitan surat.</small>
            </div>
          </div>

          @if ($suratSiap ?? false)
            <!-- Unduh Surat Jadi (gerbang nomor tiket + NIK) -->
            <div class="p-4 bg-success bg-opacity-10 rounded-4 border border-success-subtle mb-4 text-start">
              <h6 class="fw-bold text-success mb-1"><i class="bi bi-file-earmark-check me-1"></i> Surat Anda Sudah Terbit!</h6>
              <p class="text-muted small mb-3">Masukkan NIK pemohon untuk memverifikasi identitas dan mengunduh PDF surat resmi.</p>
              <form action="{{ route('surat.unduh', $tiket->nomor_tiket) }}" method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric"
                  class="form-control @error('nik') is-invalid @enderror" style="max-width: 260px;"
                  placeholder="NIK 16 digit" required>
                <button type="submit" class="btn btn-success fw-bold px-4">
                  <i class="bi bi-download me-1"></i> Unduh Surat
                </button>
                @error('nik')
                  <div class="invalid-feedback d-block w-100">{{ $message }}</div>
                @enderror
              </form>
            </div>
          @endif

          <!-- Action Buttons -->
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg rounded-3 fw-bold py-3" onclick="copyTiketCode('{{ $tiket->nomor_tiket }}')">
              <i class="bi bi-clipboard-check me-2"></i> Salin Kode Nomor Tiket
            </button>
            <a href="{{ route('cek-status.index') }}" class="btn btn-outline-primary btn-lg rounded-3 py-2">
              <i class="bi bi-search me-1"></i> Cek Progres Status Tiket
            </a>
            <a href="{{ route('surat.index') }}" class="btn btn-link text-muted mt-2">
              Ajukan Surat Lain
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
