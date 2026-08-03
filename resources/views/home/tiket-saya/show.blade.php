@extends('home.layouts.app')

@section('title', 'Tiket ' . $tiket->nomor_tiket . ' - ' . ($siteInfo['name'] ?? 'Soreang Smart Service'))
@section('body-class', 'starter-page-page')

@section('content')
<section class="section py-5" style="min-height: 60vh;">
  <div class="container" data-aos="fade-up">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <a href="{{ route('tiket-saya.index') }}" class="text-decoration-none small">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Tiket Saya
        </a>

        <div class="card border-0 shadow-sm rounded-4 mt-3 mb-4">
          <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
              <div>
                <h4 class="fw-bold mb-1">{{ $tiket->judul }}</h4>
                <code>{{ $tiket->nomor_tiket }}</code>
              </div>
              <span class="badge {{ $tiket->status->badgeClass() }} rounded-pill px-3 py-2">
                {{ $tiket->status->label() }}
              </span>
            </div>

            <table class="table table-sm table-borderless mb-0">
              <tr>
                <th class="text-muted fw-normal" width="160">Jenis Layanan</th>
                <td>
                  @if ($tiket->detail_type === 'pengajuan_surat')
                    Pengajuan Surat — {{ $tiket->detail->jenisSurat->nama ?? '-' }}
                  @else
                    Pengaduan — {{ $tiket->detail->kategori->nama ?? '-' }}
                  @endif
                </td>
              </tr>
              <tr>
                <th class="text-muted fw-normal">Tanggal Diajukan</th>
                <td>{{ $tiket->created_at->translatedFormat('d F Y H:i') }}</td>
              </tr>
              <tr>
                <th class="text-muted fw-normal">Keterangan</th>
                <td>{{ $tiket->keterangan ?: '-' }}</td>
              </tr>
              @if ($tiket->detail_type === 'pengajuan_surat' && $tiket->detail?->nomor_surat)
                <tr>
                  <th class="text-muted fw-normal">Nomor Surat</th>
                  <td><code>{{ $tiket->detail->nomor_surat }}</code></td>
                </tr>
              @endif
            </table>

            @if ($suratSiap)
              <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 mb-0">
                <span><i class="bi bi-patch-check-fill me-1"></i> Surat resmi Anda sudah terbit dan siap diunduh.</span>
                <a href="{{ route('tiket-saya.unduh', $tiket->nomor_tiket) }}" class="btn btn-success btn-sm">
                  <i class="bi bi-download me-1"></i> Unduh Surat (PDF)
                </a>
              </div>
            @endif
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-5">
          <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i> Riwayat Status</h6>
            <ul class="list-unstyled mb-0">
              @foreach ($tiket->statusLogs->sortByDesc('created_at') as $log)
                <li class="d-flex gap-3 {{ $loop->last ? '' : 'pb-3 mb-3 border-bottom' }}">
                  <div class="flex-shrink-0 pt-1">
                    <span class="badge {{ $log->status_to->badgeClass() }} rounded-pill">{{ $log->status_to->label() }}</span>
                  </div>
                  <div>
                    <small class="text-muted d-block">{{ $log->created_at->translatedFormat('d F Y H:i') }}</small>
                    @if ($log->catatan)
                      <span>{{ $log->catatan }}</span>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
