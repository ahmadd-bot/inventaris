<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%;">Nama Barang</th>
            <td>{{ $barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategori->nama_kategori }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>
                <strong>{{ $barang->jumlah_unit }}</strong> Unit
                @if($barang->detailBarangs->count() > 0)
                    <span class="ms-2">
                        <span class="badge bg-success">{{ $barang->detailBarangs->where('status', 'Tersedia')->count() }} Tersedia</span>
                        <span class="badge bg-warning text-dark">{{ $barang->detailBarangs->where('status', 'Dipinjam')->count() }} Dipinjam</span>
                        <span class="badge bg-danger">{{ $barang->detailBarangs->where('status', 'Rusak')->count() }} Rusak</span>
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Kode Unit</th>
            <td>
                @if($barang->detailBarangs->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($barang->detailBarangs as $unit)
                            @php
                                $badgeClass = 'bg-success';
                                if ($unit->status == 'Dipinjam') $badgeClass = 'bg-warning text-dark';
                                if ($unit->status == 'Rusak') $badgeClass = 'bg-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}" title="Status: {{ $unit->status }} | Kondisi: {{ $unit->kondisi }}">
                                {{ $unit->sub_kode }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">Tidak per unit</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
                @if($barang->detailBarangs->count() > 0)
                    <div class="mb-2">
                        @php
                            $kondisiDominan = $barang->kondisi_dominan;
                            $badgeClass = 'bg-success';
                            if ($kondisiDominan == 'Rusak ringan') $badgeClass = 'bg-warning text-dark';
                            if ($kondisiDominan == 'Rusak berat') $badgeClass = 'bg-danger';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $kondisiDominan }}</span>
                        <small class="text-muted ms-1">(Kondisi Dominan)</small>
                    </div>
                    <small class="text-muted">
                        Detail: 
                        {{ $barang->detailBarangs->where('kondisi', 'Baik')->count() }} Baik, 
                        {{ $barang->detailBarangs->where('kondisi', 'Rusak ringan')->count() }} Rusak Ringan, 
                        {{ $barang->detailBarangs->where('kondisi', 'Rusak berat')->count() }} Rusak Berat
                    </small>
                @else
                    @php
                        $badgeClass = 'bg-success';
                        if ($barang->kondisi == 'Rusak Ringan') $badgeClass = 'bg-warning text-dark';
                        if ($barang->kondisi == 'Rusak Berat') $badgeClass = 'bg-danger';
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $barang->kondisi ?? 'Baik' }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Tanggal Pengadaan</th>
            <td>{{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $barang->updated_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>
    </tbody>
</table>