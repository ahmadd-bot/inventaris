<x-main-layout :title-page="'Detail Unit: ' . $detailBarang->sub_kode">
    <div class="card my-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @include('barang.partials.info-gambar-barang', ['barang' => $detailBarang->barang])
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Nama Barang</th>
                                <td>{{ $detailBarang->barang->nama_barang }}</td>
                            </tr>
                            <tr>
                                <th>Kode Unit</th>
                                <td>
                                    <strong class="text-primary fs-5">{{ $detailBarang->sub_kode }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $detailBarang->barang->kategori->nama_kategori }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $detailBarang->barang->lokasi->nama_lokasi }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($detailBarang->status === 'Tersedia')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Tersedia
                                        </span>
                                    @elseif ($detailBarang->status === 'Dipinjam')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Dipinjam
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Rusak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kondisi</th>
                                <td>
                                    @php
                                        $badgeClass = 'bg-success';
                                        if ($detailBarang->kondisi == 'Rusak ringan') $badgeClass = 'bg-warning text-dark';
                                        if ($detailBarang->kondisi == 'Rusak berat') $badgeClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $detailBarang->kondisi }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $detailBarang->keterangan ?? '-' }}</td>
                            </tr>
                            
                            {{-- Info Peminjaman Jika Sedang Dipinjam --}}
                            @if($detailBarang->peminjamanAktif)
                                <tr class="table-warning">
                                    <th colspan="2" class="text-center">
                                        <i class="bi bi-info-circle"></i> Informasi Peminjaman
                                    </th>
                                </tr>
                                <tr>
                                    <th>Nama Peminjam</th>
                                    <td><strong>{{ $detailBarang->peminjamanAktif->nama_peminjam }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pinjam</th>
                                    <td>{{ $detailBarang->peminjamanAktif->tanggal_pinjam->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kembali</th>
                                    <td>
                                        @if($detailBarang->peminjamanAktif->tanggal_kembali)
                                            {{ $detailBarang->peminjamanAktif->tanggal_kembali->translatedFormat('d F Y') }}
                                        @else
                                            <span class="text-muted">Belum ditentukan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            
                            <tr>
                                <th>Tanggal Pengadaan</th>
                                <td>{{ \Carbon\Carbon::parse($detailBarang->barang->tanggal_pengadaan)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ $detailBarang->updated_at->translatedFormat('d F Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('barang.units.index', $barang) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Kelola Unit
                </a>
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list"></i> Ke Daftar Barang
                </a>
            </div>
        </div>
    </div>
</x-main-layout>