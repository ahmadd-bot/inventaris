<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>Status Pinjam</th>
            <th>Sumber Dana</th>
            <th>Tgl. Pengadaan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barangs as $index => $barang)
            @if ($barang->is_per_unit)
                {{-- Tampilan untuk Barang Per Unit --}}
                @if ($barang->detailBarangs->isEmpty())
                    {{-- Jika tidak ada unit, tampilkan 1 baris dengan info barang induk --}}
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kategori->nama_kategori }}</td>
                        <td>{{ $barang->lokasi->nama_lokasi }}</td>
                        <td><strong>Per Unit</strong></td>
                        <td>0 {{ $barang->satuan }}</td>
                        <td>-</td>
                        <td>{{ $barang->status_pinjam }}</td>
                        <td>{{ $barang->sumber_dana }}</td>
                        <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
                    </tr>
                @else
                    {{-- Jika ada unit, tampilkan data induk terlebih dahulu --}}
                    <tr style="background-color: #e8f4f8;">
                        <td colspan="11">
                            <strong>{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</strong> 
                            (Per Unit - Total: {{ $barang->detailBarangs->count() }} Unit)
                        </td>
                    </tr>

                    {{-- Tampilkan setiap unit sebagai sub-row --}}
                    @foreach ($barang->detailBarangs as $subIndex => $unit)
                        <tr>
                            <td>{{ $index }}.{{ $subIndex + 1 }}</td>
                            <td>{{ $unit->sub_kode }}</td>
                            <td style="padding-left: 30px;">
                                <em>{{ $barang->nama_barang }}</em>
                            </td>
                            <td>{{ $barang->kategori->nama_kategori }}</td>
                            <td>{{ $unit->barang->lokasi->nama_lokasi }}</td>
                            <td>Unit</td>
                            <td>1 {{ $barang->satuan }}</td>
                            <td>{{ $unit->kondisi }}</td>
                            <td>
                                @if ($barang->status_pinjam === 'Dapat Dipinjam')
                                    {{ $unit->status }}
                                @else
                                    Tidak Dapat Dipinjam
                                @endif
                            </td>
                            <td>{{ $barang->sumber_dana }}</td>
                            <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
                        </tr>
                    @endforeach
                @endif
            @else
                {{-- Tampilan untuk Barang Tidak Per Unit --}}
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->kategori->nama_kategori }}</td>
                    <td>{{ $barang->lokasi->nama_lokasi }}</td>
                    <td>Tidak Per Unit</td>
                    <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
                    <td>{{ $barang->kondisi }}</td>
                    <td>{{ $barang->status_pinjam }}</td>
                    <td>{{ $barang->sumber_dana }}</td>
                    <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="11" class="text-center"><strong>Tidak ada data barang.</strong></td>
            </tr>
        @endforelse
    </tbody>
</table>