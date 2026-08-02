{{-- 1. Modal Daftar Jenis Surat --}}
<div class="modal fade" id="jenis-surat-modal" tabindex="-1" aria-labelledby="jenisSuratModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3 px-4">
        <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="jenisSuratModalLabel">
          <i class="bi bi-file-earmark-text me-2 fs-5"></i> Daftar Jenis Surat Keterangan Online 3S
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="p-3 bg-light rounded-3 mb-4 border border-start border-4 border-primary">
          <p class="text-secondary small mb-0">
            <i class="bi bi-info-circle text-primary me-1"></i> Berikut daftar jenis surat administrasi kependudukan yang dapat diajukan secara digital via <strong>Soreang Smart Service (3S)</strong>.
          </p>
        </div>
        <div class="row g-3">
          @forelse($jenisSuratList ?? [] as $js)
            <div class="col-md-6">
              <div class="p-3 border rounded-3 bg-white shadow-sm h-100 transition-all">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">{{ $js->kode }}</span>
                  @if($js->wajib_pengantar_rt_rw)
                    <small class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-exclamation-circle me-1"></i> Wajib RT/RW</small>
                  @else
                    <small class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Langsung Proses</small>
                  @endif
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $js->nama }}</h6>
                <p class="text-muted small mb-0" style="font-size: 0.82rem;">{{ $js->deskripsi }}</p>
              </div>
            </div>
          @empty
            <div class="col-12 text-center py-4">
              <p class="text-muted mb-0">Belum ada daftar jenis surat aktif.</p>
            </div>
          @endforelse
        </div>
      </div>
      <div class="modal-footer bg-light py-3 px-4 border-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">Pengajuan Sekarang <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>
</div>

{{-- 2. Modal Jadwal Pelayanan Kelurahan --}}
<div class="modal fade" id="jadwal-pelayanan-modal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3 px-4">
        <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="jadwalModalLabel">
          <i class="bi bi-clock-history me-2 fs-5"></i> Jadwal Pelayanan Kelurahan Se-Kecamatan Soreang
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border">
          <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
          <input type="text" id="s3SearchJadwal" class="form-control border-0 pe-3" placeholder="Cari nama kelurahan (contoh: Lakessi, Bukit Harapan)...">
        </div>
        <div class="table-responsive rounded-3 border">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="text-secondary small fw-bold">
                <th class="py-3 px-3">Kelurahan</th>
                <th class="py-3 px-3">Jam Operasional</th>
                <th class="py-3 px-3">Istirahat</th>
                <th class="py-3 px-3">Petugas PJ</th>
                <th class="py-3 px-3">Telepon</th>
              </tr>
            </thead>
            <tbody id="s3JadwalTableBody">
              @forelse($jadwalList ?? [] as $j)
                <tr>
                  <td class="fw-bold text-primary py-3 px-3">{{ $j->kelurahan->nama ?? '-' }}</td>
                  <td class="py-3 px-3"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">{{ $j->jam_buka }} - {{ $j->jam_tutup }}</span></td>
                  <td class="py-3 px-3"><small class="text-muted">{{ $j->istirahat ?? '-' }}</small></td>
                  <td class="py-3 px-3"><small class="fw-semibold text-dark">{{ $j->petugas ?? '-' }}</small></td>
                  <td class="py-3 px-3"><small class="text-muted">{{ $j->telepon ?? '-' }}</small></td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Jadwal pelayanan belum diinput.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light py-3 px-4 border-0">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- 3. Modal QR Scanner Simulator --}}
<div class="modal fade" id="s3QrScannerModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center">
      <div class="modal-header bg-primary text-white py-3 px-4">
        <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="qrModalLabel">
          <i class="bi bi-qr-code-scan me-2 fs-5"></i> QR Code Scanner 3S
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="p-4 bg-light border border-2 border-dashed rounded-4 mb-3 position-relative">
          <i class="bi bi-camera display-3 text-primary opacity-50 d-block mb-2"></i>
          <p class="text-muted small mb-0">Arahkan kamera ke QR Code Tiket / Dokumen 3S</p>
        </div>
        <p class="small text-muted mb-2">Atau gunakan sampel tiket berikut untuk simulasi:</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="s3SimulateQrInput('SRG-2607-00123')">SRG-2607-00123</button>
          <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="s3SimulateQrInput('3204012345670001')">NIK 3204012345670001</button>
        </div>
      </div>
      <div class="modal-footer bg-light py-2 px-4 border-0">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

{{-- 4. Modal Pop Up Hasil Cek Status --}}
<div class="modal fade" id="modalCekStatusResult" tabindex="-1" aria-labelledby="modalCekStatusResultLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3 px-4">
        <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" id="modalCekStatusResultLabel">
          <i class="bi bi-ticket-perforated me-2 fs-5"></i> Hasil Cek Status Permohonan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" id="s3TicketModalBody">
        <!-- Live AJAX content will be injected dynamically -->
      </div>
      <div class="modal-footer bg-light border-0 py-3 px-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
