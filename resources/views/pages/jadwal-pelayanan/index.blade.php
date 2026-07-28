@extends('layouts/layoutMaster')

@section('title', 'Jadwal Pelayanan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Jadwal Pelayanan
        </h4>
        <a href="{{ route('jadwal-pelayanan.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Jadwal
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Jadwal Operasional Kelurahan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Kelurahan</th>
                        <th>Jam Buka - Tutup</th>
                        <th>Istirahat</th>
                        <th>Hari Operasional</th>
                        <th>Petugas PJ</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-primary">{{ $item->kelurahan->nama ?? '-' }}</span></td>
                            <td><span class="badge bg-label-success fs-6">{{ $item->jam_buka }} - {{ $item->jam_tutup }}</span></td>
                            <td><small class="text-muted">{{ $item->istirahat ?? '-' }}</small></td>
                            <td><small class="fw-semibold">{{ implode(', ', $item->hari ?? []) }}</small></td>
                            <td><small>{{ $item->petugas ?? '-' }}</small></td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('jadwal-pelayanan.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('jadwal-pelayanan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->kelurahan->nama ?? '' }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-calendar-event-line ri-3x mb-2"></i>
                                    <p>Belum ada data Jadwal Pelayanan yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.delete-record').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let url = "{{ route('jadwal-pelayanan.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Jadwal Pelayanan?',
                `Apakah Anda yakin ingin menghapus jadwal "${name}"?`,
                'Ya, Hapus!',
                function() {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        headers: { 'Accept': 'application/json' },
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            window.AlertHandler.handle(response);
                            setTimeout(() => { window.location.reload(); }, 1500);
                        },
                        error: function(xhr) {
                            window.AlertHandler.handle(xhr.responseJSON);
                        }
                    });
                }
            );
        });
    });
</script>
@endsection
