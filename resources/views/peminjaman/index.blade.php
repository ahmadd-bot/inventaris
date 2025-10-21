<x-main-layout title-page="Data Peminjaman Barang">
    <div class="card">
        {{-- Header Section --}}
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">Daftar Peminjaman</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Pinjam Per Unit
                    </a>
                    <a href="{{ route('peminjaman.createNotPerUnit') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Pinjam Tidak Per Unit
                    </a>
                    <a href="{{ route('peminjaman.cetakLaporan') }}" class="btn btn-success" target="_blank">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Search & Filter --}}
            <form method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Cari peminjam / barang / kode..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value=""> Semua Status</option>
                            <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}> Sedang Dipinjam</option>
                            <option value="Dikembalikan" {{ request('status') === 'Dikembalikan' ? 'selected' : '' }}> Sudah Dikembalikan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Barang</th>
                            <th width="12%">Kode Unit</th>
                            <th width="18%">Peminjam</th>
                            <th width="12%">Tgl. Pinjam</th>
                            <th width="12%">Tgl. Kembali</th>
                            <th width="10%">Status</th>
                            <th width="11%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $index => $peminjaman)
                            <tr>
                                <td>{{ $peminjamans->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $peminjaman->detailBarang->barang->nama_barang }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $peminjaman->detailBarang->sub_kode }}</span>
                                </td>
                                <td>
                                    <strong>{{ $peminjaman->nama_peminjam }}</strong>
                                    @if ($peminjaman->kontak)
                                        <br><small class="text-muted">{{ $peminjaman->kontak }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if ($peminjaman->tanggal_kembali)
                                        <small>{{ $peminjaman->tanggal_kembali->format('d/m/Y') }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Belum</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($peminjaman->status === 'Dipinjam')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Dipinjam
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Dikembalikan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($peminjaman->status === 'Dipinjam')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#modalKembalikan{{ $peminjaman->id }}"
                                            title="Kembalikan Barang">
                                            <i class="bi bi-arrow-return-left"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled
                                            title="Sudah dikembalikan">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    @endif
                                    
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" data-url="{{ route('peminjaman.destroy', $peminjaman) }}"
                                        title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Kembalikan --}}
                            @if ($peminjaman->status === 'Dipinjam')
                                <div class="modal fade" id="modalKembalikan{{ $peminjaman->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('peminjaman.kembalikan', $peminjaman) }}">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-arrow-return-left"></i> Kembalikan Barang
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3 p-3 bg-light rounded">
                                                        <small class="text-muted">Barang yang dikembalikan:</small>
                                                        <p class="mb-0">
                                                            <strong>{{ $peminjaman->detailBarang->barang->nama_barang }}</strong><br>
                                                            <span class="badge bg-secondary">{{ $peminjaman->detailBarang->sub_kode }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                                        <input type="date" name="tanggal_kembali" class="form-control" 
                                                            value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan (Kondisi Barang)</label>
                                                        <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Dalam kondisi baik / Ada kerusakan..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-circle"></i> Kembalikan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-3">Tidak ada data peminjaman</p>
                                    <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-circle"></i> Mulai Peminjaman
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $peminjamans->links() }}
            </div>
        </div>
    </div>
</x-main-layout>