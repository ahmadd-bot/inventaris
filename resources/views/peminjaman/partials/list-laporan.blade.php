<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Unit</th>
            <th>Nama Barang</th>
            <th>Peminjam</th>
            <th>Jumlah</th>
            <th>Tgl. Pinjam</th>
            <th>Tgl. Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($peminjamans as $index => $peminjaman)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $peminjaman->detailBarang->sub_kode }}</td>
                <td>{{ $peminjaman->detailBarang->barang->nama_barang }}</td>
                <td>
                    <strong>{{ $peminjaman->nama_peminjam }}</strong>
                    @if ($peminjaman->kontak)
                        <br><small>{{ $peminjaman->kontak }}</small>
                    @endif
                </td>
                <td>{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->detailBarang->barang->satuan }}</td>
                <td>{{ date('d/m/Y', strtotime($peminjaman->tanggal_pinjam)) }}</td>
                <td>
                    @if ($peminjaman->tanggal_kembali)
                        {{ date('d/m/Y', strtotime($peminjaman->tanggal_kembali)) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $peminjaman->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center"><strong>Tidak ada data peminjaman.</strong></td>
            </tr>
        @endforelse
    </tbody>
</table>