@extends('layouts/layoutMaster')

@section('title', 'Berita & Informasi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Berita & Informasi
        </h4>
        <a href="{{ route('berita.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Berita
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Berita & Pengumuman</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Gambar</th>
                        <th>Judul & Ringkasan</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
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
                                    $imgPath = $item->thumbnail ?? $item->gambar;
                                    if ($imgPath && !Str::startsWith($imgPath, 'http')) {
                                        $imgPath = Str::startsWith($imgPath, 'storage/') ? asset($imgPath) : asset('storage/' . ltrim($imgPath, '/'));
                                    }
                                @endphp
                                @if($imgPath)
                                    <img src="{{ $imgPath }}" alt="{{ $item->judul }}" width="60" height="40" class="rounded object-fit-cover">
                                @else
                                    <span class="badge bg-label-secondary">No Image</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold d-block">{{ $item->judul }}</span>
                                @if ($item->ringkasan)
                                    <small class="text-muted">{{ str($item->ringkasan)->limit(70) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-label-info">{{ $item->kategori }}</span></td>
                            <td><small class="fw-semibold">{{ $item->penulis ?? 'Admin' }}</small></td>
                            <td>
                                @if ($item->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('berita.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('berita.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
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
                                    <i class="ri-newspaper-line ri-3x mb-2"></i>
                                    <p>Belum ada data Berita yang tersedia.</p>
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
            let url = "{{ route('berita.destroy', ':id') }}".replace(':id', id);

            window.AlertHandler.confirm(
                'Hapus Berita?',
                `Apakah Anda yakin ingin menghapus "${name}"?`,
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
