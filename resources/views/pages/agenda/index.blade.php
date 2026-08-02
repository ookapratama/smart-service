@extends('layouts/layoutMaster')

@section('title', 'Agenda Kegiatan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Agenda Kegiatan
        </h4>
        <a href="{{ route('agenda.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Agenda
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Agenda & Kalender Kegiatan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Poster</th>
                        <th>Judul & Lokasi</th>
                        <th>Waktu Kegiatan</th>
                        <th>Penyelenggara</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @php
                                    $imgPath = $item->gambar;
                                    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                                        $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                                    }
                                @endphp
                                @if($imgPath)
                                    <img src="{{ $imgPath }}" alt="{{ $item->judul }}" width="60" height="40" class="rounded object-fit-cover">
                                @else
                                    <span class="badge bg-label-secondary">No Poster</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold d-block">{{ $item->judul }}</span>
                                <small class="text-muted"><i class="ri-map-pin-line me-1"></i> {{ $item->lokasi ?? 'Kantor Kecamatan Soreang' }}</small>
                            </td>
                            <td>
                                <span class="d-block text-dark font-monospace small fw-semibold">
                                    {{ optional($item->mulai_at)->format('d M Y H:i') }}
                                </span>
                                @if($item->waktu_keterangan)
                                    <small class="text-muted">{{ $item->waktu_keterangan }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-label-info">{{ $item->penyelenggara ?? 'Kecamatan Soreang' }}</span></td>
                            <td>
                                @if(optional($item->mulai_at)->isFuture())
                                    <span class="badge bg-warning">Mendatang</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('agenda.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('agenda.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->judul }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-calendar-todo-line ri-3x mb-2"></i>
                                    <p>Belum ada data Agenda Kegiatan yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-record').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                if (confirm(`Apakah Anda yakin ingin menghapus agenda "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `{{ url('admin/agenda') }}/${id}`;
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
