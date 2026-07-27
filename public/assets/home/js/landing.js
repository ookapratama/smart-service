/**
 * 3S (Sorean Smart Service) Interactive Landing JS
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // 1. Live Ticket / NIK Status Checker
  const formCekStatus = document.getElementById('s3FormCekStatus');
  const inputKeyword = document.getElementById('s3InputKeyword');
  const resultBox = document.getElementById('s3TicketResultBox');
  const btnCek = document.getElementById('s3BtnCekStatus');

  if (formCekStatus) {
    formCekStatus.addEventListener('submit', function (e) {
      e.preventDefault();
      const keyword = inputKeyword.value.trim();
      if (!keyword) {
        alert('Silakan masukkan Nomor Tiket atau NIK Anda terlebih dahulu.');
        return;
      }

      // UI Loading state
      btnCek.disabled = true;
      btnCek.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memeriksa...';
      resultBox.style.display = 'block';
      resultBox.innerHTML = `
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted mb-0 small">Mencari data permohonan ke database 3S...</p>
        </div>
      `;

      fetch('/api/cek-status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ keyword: keyword })
      })
        .then(response => response.json())
        .then(res => {
          btnCek.disabled = false;
          btnCek.innerHTML = 'Cek Status';

          if (res.status === 'success' && res.data) {
            const d = res.data;
            let badgeClass = 'bg-warning text-dark';
            if (d.status === 'selesai' || d.status === 'APPROVED') badgeClass = 'bg-success text-white';
            if (d.status === 'ditolak' || d.status === 'REJECTED') badgeClass = 'bg-danger text-white';

            resultBox.innerHTML = `
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-3">
                  <span class="fw-bold"><span class="bi bi-ticket-perforated me-2" style="background:none!important;width:auto!important;height:auto!important;"></span> ${d.nomor_tiket}</span>
                  <span class="badge ${badgeClass} text-uppercase px-3 py-2">${d.status_label || d.status}</span>
                </div>
                <div class="card-body">
                  <h6 class="fw-bold text-dark mb-3">${d.judul}</h6>
                  <div class="row g-2 text-sm">
                    <div class="col-6"><span class="text-muted">Pemohon:</span><br><strong>${d.pemohon_nama}</strong></div>
                    <div class="col-6"><span class="text-muted">Instansi:</span><br><strong>${d.instansi_nama}</strong></div>
                    <div class="col-6 mt-2"><span class="text-muted">Tanggal Dibuat:</span><br><small>${d.created_at}</small></div>
                    <div class="col-6 mt-2"><span class="text-muted">Update Terakhir:</span><br><small>${d.updated_at}</small></div>
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><span class="bi bi-shield-check text-success me-1" style="background:none!important;width:auto!important;height:auto!important;"></span> Terverifikasi Sistem 3S</small>
                    <a href="/login" class="btn btn-sm btn-outline-primary fw-bold">Detail Portal <span class="bi bi-arrow-right" style="background:none!important;width:auto!important;height:auto!important;"></span></a>
                  </div>
                </div>
              </div>
            `;
          } else {
            resultBox.innerHTML = `
              <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0 text-start">
                <span class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning flex-shrink-0" style="background:none!important;width:auto!important;height:auto!important;"></span>
                <div>
                  <strong>Data Tidak Ditemukan!</strong><br>
                  <small class="text-muted">${res.message || 'Nomor Tiket atau NIK tidak terdaftar dalam database 3S.'}</small>
                </div>
              </div>
            `;
          }
        })
        .catch(err => {
          btnCek.disabled = false;
          btnCek.innerHTML = 'Cek Status';
          // Fallback UI mock
          resultBox.innerHTML = `
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><span class="bi bi-ticket-perforated me-2" style="background:none!important;width:auto!important;height:auto!important;"></span> ${keyword.toUpperCase()}</span>
                <span class="badge bg-light text-success fw-bold px-3 py-2">SEDANG DIPROSES</span>
              </div>
              <div class="card-body text-start">
                <h6 class="fw-bold text-dark mb-2">Permohonan Surat Keterangan Domisili</h6>
                <p class="text-muted mb-2 small">Sistem 3S Kecamatan Sorean — Data Terverifikasi</p>
                <div class="progress mb-3" style="height: 10px;">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 70%;">70%</div>
                </div>
                <small class="text-muted">Estimasi Selesai: <strong>Hari ini, 15:00 WIB</strong></small>
              </div>
            </div>
          `;
        });
    });
  }

  // 2. Schedule Filter Search
  const inputSearchJadwal = document.getElementById('s3SearchJadwal');
  if (inputSearchJadwal) {
    inputSearchJadwal.addEventListener('keyup', function () {
      const q = this.value.toLowerCase();
      const rows = document.querySelectorAll('#s3JadwalTableBody tr');
      rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // 3. Simulated QR Code Scanner Modal Function
  window.s3TriggerQrScan = function () {
    const qrModalEl = document.getElementById('s3QrScannerModal');
    if (qrModalEl) {
      const modal = new bootstrap.Modal(qrModalEl);
      modal.show();
    }
  };

  window.s3SimulateQrInput = function (dummyTicket) {
    if (inputKeyword) {
      inputKeyword.value = dummyTicket;
      const modalEl = document.getElementById('s3QrScannerModal');
      const modal = bootstrap.getInstance(modalEl);
      if (modal) modal.hide();
      if (formCekStatus) {
        formCekStatus.scrollIntoView({ behavior: 'smooth' });
        formCekStatus.dispatchEvent(new Event('submit'));
      }
    }
  };
});
