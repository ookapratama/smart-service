<div id="tiketGridContent">
  @forelse ($tikets as $tiket)
    <a href="{{ route('tiket-saya.show', $tiket->nomor_tiket) }}"
       class="card border-0 shadow-sm rounded-4 mb-3 text-decoration-none text-body hover-scale">
      <div class="card-body p-3 p-md-4 d-flex flex-wrap align-items-center gap-3">
        <div class="flex-shrink-0 bg-light rounded-3 d-flex align-items-center justify-content-center"
             style="width: 48px; height: 48px;">
          <i class="bi {{ $tiket->detail_type === 'pengajuan_surat' ? 'bi-file-earmark-text' : 'bi-megaphone' }} fs-4 text-primary"></i>
        </div>
        <div class="flex-grow-1">
          <div class="fw-semibold">{{ $tiket->judul }}</div>
          <small class="text-muted">
            <code>{{ $tiket->nomor_tiket }}</code> &middot;
            {{ $tiket->detail_type === 'pengajuan_surat' ? 'Pengajuan Surat' : 'Pengaduan' }} &middot;
            {{ $tiket->created_at->translatedFormat('d M Y H:i') }}
          </small>
        </div>
        <span class="badge {{ $tiket->status->badgeClass() }} rounded-pill px-3 py-2">
          {{ $tiket->status->label() }}
        </span>
      </div>
    </a>
  @empty
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
        <p class="text-muted mb-3">Belum ada tiket. Mulai dengan mengajukan surat atau membuat pengaduan.</p>
        <a href="{{ route('surat.index') }}" class="btn btn-primary rounded-pill px-4">Ajukan Surat Online</a>
      </div>
    </div>
  @endforelse
</div>

@if($tikets->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $tikets->links('pagination::bootstrap-5') }}
  </div>
@endif
