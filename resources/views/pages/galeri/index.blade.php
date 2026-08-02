@extends('layouts/layoutMaster')

@section('title', 'Galeri Foto & Video')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Galeri Foto & Video
        </h4>
        <a href="{{ route('galeri.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Galeri
        </a>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Galeri Foto & Video</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Media</th>
                        <th>Judul & Keterangan</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
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
                                    <span class="badge bg-label-secondary">No Media</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold d-block">{{ $item->judul }}</span>
                                @if ($item->keterangan)
                                    <small class="text-muted">{{ str($item->keterangan)->limit(70) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-label-info">{{ $item->kategori }}</span></td>
                            <td>
                                @if($item->tipe === 'video')
                                    <span class="badge bg-label-warning"><i class="ri-video-line me-1"></i> Video</span>
                                @else
                                    <span class="badge bg-label-primary"><i class="ri-image-line me-1"></i> Foto</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('galeri.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('galeri.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
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
                                    <i class="ri-gallery-line ri-3x mb-2"></i>
                                    <p>Belum ada data Galeri yang tersedia.</p>
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
                
                if (confirm(`Apakah Anda yakin ingin menghapus galeri "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `{{ url('admin/galeri') }}/${id}`;
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
