/**
 * 3S (Soreang Smart Service) Interactive Landing JS
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Helper for safe Bootstrap Modal Instance
  function getOrNewModal(element) {
    if (!element) return null;
    var bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
    if (bs && bs.Modal) {
      return bs.Modal.getInstance(element) || new bs.Modal(element);
    }
    return null;
  }

  // 1. Live Ticket / NIK Status Checker (Pop-Up Modal)
  const formCekStatus = document.getElementById('s3FormCekStatus');
  const inputKeyword = document.getElementById('s3InputKeyword');
  const btnCek = document.getElementById('s3BtnCekStatus');

  if (formCekStatus) {
    formCekStatus.addEventListener('submit', function (e) {
      e.preventDefault();
      const keyword = inputKeyword ? inputKeyword.value.trim() : '';
      if (!keyword) {
        alert('Silakan masukkan Nomor Tiket atau NIK Anda terlebih dahulu.');
        return;
      }

      let targetModal = document.getElementById('modalCekStatusResult');
      if (!targetModal) {
        // Fallback: create modal dynamically if missing on page
        const modalDiv = document.createElement('div');
        modalDiv.className = 'modal fade';
        modalDiv.id = 'modalCekStatusResult';
        modalDiv.tabIndex = -1;
        modalDiv.innerHTML = `
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
              <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold text-white mb-0">
                  <i class="bi bi-ticket-perforated me-2 text-white"></i> Hasil Cek Status Permohonan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4" id="s3TicketModalBody"></div>
              <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(modalDiv);
        targetModal = modalDiv;
      } else if (targetModal.parentNode !== document.body) {
        document.body.appendChild(targetModal);
      }

      const targetBody = document.getElementById('s3TicketModalBody');

      // UI Loading state inside Modal
      if (btnCek) {
        btnCek.disabled = true;
        btnCek.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memeriksa...';
      }

      if (targetBody) {
        targetBody.innerHTML = `
          <div class="text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
            <p class="mt-3 text-muted mb-0 fw-semibold">Mencari data permohonan ke database 3S...</p>
          </div>
        `;
      }

      const bsModal = getOrNewModal(targetModal);
      if (bsModal) {
        bsModal.show();
      }

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
          if (btnCek) {
            btnCek.disabled = false;
            btnCek.innerHTML = 'Cek Status';
          }

          if (res.status === 'success' && res.data) {
            const d = res.data;
            let badgeClass = 'bg-warning text-dark';
            if (d.status === 'selesai' || d.status === 'APPROVED') badgeClass = 'bg-success text-white';
            if (d.status === 'ditolak' || d.status === 'REJECTED') badgeClass = 'bg-danger text-white';

            let suratHtml = '';
            if (d.surat_url) {
              suratHtml = `
                <div class="mt-3 p-3 bg-success bg-opacity-10 rounded-3 border border-success-subtle text-start">
                  <small class="text-success fw-bold d-block mb-1"><i class="bi bi-file-earmark-check me-1"></i> Surat Anda Sudah Terbit!</small>
                  <a href="${d.surat_url}" class="btn btn-success btn-sm fw-bold px-3 mt-1">
                    <i class="bi bi-download me-1"></i> Unduh Surat (verifikasi NIK)
                  </a>
                </div>
              `;
            }

            let responHtml = '';
            if (d.catatan_respon) {
              responHtml = `
                <div class="mt-3 p-3 bg-light rounded-3 border-start border-4 border-primary text-start">
                  <small class="text-primary fw-bold d-block mb-1"><i class="bi bi-person-badge me-1"></i> Respon / Catatan Petugas 3S:</small>
                  <small class="text-dark d-block fs-6">${d.catatan_respon}</small>
                </div>
              `;
            }

            if (targetBody) {
              targetBody.innerHTML = `
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                  <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5"><i class="bi bi-ticket-perforated me-2"></i> ${d.nomor_tiket}</span>
                    <span class="badge ${badgeClass} text-uppercase px-3 py-2 fs-6 rounded-pill">${d.status_label || d.status}</span>
                  </div>
                  <div class="card-body p-4 text-start">
                    <h5 class="fw-bold text-dark mb-3">${d.judul}</h5>
                    <div class="row g-3 p-3 bg-light rounded-3 mb-3 border">
                      <div class="col-sm-6"><span class="text-muted small">Nama Pemohon:</span><br><strong class="text-dark">${d.pemohon_nama}</strong></div>
                      <div class="col-sm-6"><span class="text-muted small">Unit Layanan:</span><br><strong class="text-dark">${d.instansi_nama}</strong></div>
                      <div class="col-sm-6"><span class="text-muted small">Tanggal Dibuat:</span><br><strong>${d.created_at}</strong></div>
                      <div class="col-sm-6"><span class="text-muted small">Update Terakhir:</span><br><strong>${d.updated_at}</strong></div>
                    </div>
                    ${responHtml}
                    ${suratHtml}
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                      <span class="text-success small fw-semibold"><i class="bi bi-shield-check me-1"></i> Terverifikasi Resmi Sistem 3S</span>
                      <a href="/login" class="btn btn-outline-primary btn-sm fw-bold px-3">
                        Masuk Portal Petugas <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                    </div>
                  </div>
                </div>
              `;
            }
          } else {
            if (targetBody) {
              targetBody.innerHTML = `
                <div class="alert alert-warning border-0 shadow-sm p-4 rounded-4 mb-0 text-start d-flex align-items-start">
                  <i class="bi bi-exclamation-triangle-fill fs-1 me-3 text-warning flex-shrink-0"></i>
                  <div>
                    <h5 class="fw-bold text-dark mb-1">Data Tidak Ditemukan</h5>
                    <p class="mb-0 text-muted">${res.message || 'Nomor Tiket atau NIK tidak terdaftar pada database sistem.'}</p>
                  </div>
                </div>
              `;
            }
          }
        })
        .catch(err => {
          if (btnCek) {
            btnCek.disabled = false;
            btnCek.innerHTML = 'Cek Status';
          }
          if (targetBody) {
            targetBody.innerHTML = `
              <div class="alert alert-danger border-0 shadow-sm p-4 rounded-4 mb-0 text-start d-flex align-items-start">
                <i class="bi bi-x-circle-fill fs-1 me-3 text-danger flex-shrink-0"></i>
                <div>
                  <h5 class="fw-bold text-dark mb-1">Terjadi Kesalahan</h5>
                  <p class="mb-0 text-muted">Gagal terhubung ke server. Silakan coba beberapa saat lagi.</p>
                </div>
              </div>
            `;
          }
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
    const modal = getOrNewModal(qrModalEl);
    if (modal) {
      modal.show();
    }
  };

  window.s3SimulateQrInput = function (dummyTicket) {
    if (inputKeyword) {
      inputKeyword.value = dummyTicket;
      const modalEl = document.getElementById('s3QrScannerModal');
      const modal = getOrNewModal(modalEl);
      if (modal) modal.hide();
      if (formCekStatus) {
        formCekStatus.scrollIntoView({ behavior: 'smooth' });
        formCekStatus.dispatchEvent(new Event('submit'));
      }
    }
  };
});
