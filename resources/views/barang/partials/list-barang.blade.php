<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Kondisi</th>
            <th>Status Pinjam</th>
            <th>Sumber Dana</th>
            <th>Jumlah</th>
            <th>Tipe</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($barangs as $index => $barang)
        <tr>
            <td>{{ $barangs->firstItem() + $index }}</td>
            <td><strong class="text-primary">{{ $barang->kode_barang }}</strong></td>
            <td>
                {{ $barang->nama_barang }}
                {{-- Notifikasi peminjaman aktif untuk barang tidak per unit --}}
                @if (!$barang->is_per_unit && $barang->status_pinjam === 'Dapat Dipinjam')
                    @php
                        $peminjamanAktif = \App\Models\Peminjaman::whereHas('detailBarang', function($q) use ($barang) {
                            $q->where('barang_id', $barang->id);
                        })->where('status', 'Dipinjam')->first();
                    @endphp
                    @if ($peminjamanAktif)
                        <br>
                        <small>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-circle"></i> 
                                Sedang Dipinjam: {{ $peminjamanAktif->jumlah_pinjam }} {{ $barang->satuan }}
                            </span>
                        </small>
                    @endif
                @endif
            </td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
            {{-- ===================== TABEL UTAMA ===================== --}}
<td>
    @php
        // Default ambil kondisi dari kolom barang
        $kondisiText = $barang->kondisi;
        $badgeClass = 'bg-success';

        // Jika per unit tapi belum ada detail → tampilkan strip
        if ($barang->is_per_unit && $barang->detailBarangs->isEmpty()) {
            $kondisiText = '–';
            $badgeClass = 'bg-secondary';
        }
        // Jika kondisi null atau kosong → tampilkan strip
        elseif (is_null($kondisiText) || trim($kondisiText) === '') {
            $kondisiText = '–';
            $badgeClass = 'bg-secondary';
        }
        // Jika rusak ringan
        elseif ($kondisiText === 'Rusak ringan') {
            $badgeClass = 'bg-warning text-dark';
        }
        // Jika rusak berat
        elseif ($kondisiText === 'Rusak berat') {
            $badgeClass = 'bg-danger';
        }
        // Jika baik
        elseif ($kondisiText === 'Baik') {
            $badgeClass = 'bg-success';
        }
    @endphp

    <span class="badge {{ $badgeClass }}">{{ $kondisiText }}</span>
</td>

            <td>
                @php
                    $pinjamBadge = $barang->status_pinjam === 'Dapat Dipinjam' ? 'bg-success' : 'bg-danger';
                @endphp
                <span class="badge {{ $pinjamBadge }}">
                    @if ($barang->status_pinjam === 'Dapat Dipinjam')
                        <i class="bi bi-check-circle"></i>
                    @else
                        <i class="bi bi-x-circle"></i>
                    @endif
                    {{ $barang->status_pinjam }}
                </span>
            </td>
            <!-- Kolom Sumber Dana - Tanpa Warna -->
<td>
    {{ $barang->sumber_dana }}
</td>

<!-- Kolom Jumlah - Tanpa Warna -->
<td>
    @if ($barang->is_per_unit)
        {{ $barang->jumlah_unit }} {{ $barang->satuan }}
    @else
        {{ $barang->jumlah }} {{ $barang->satuan }}
    @endif
</td>
            <td>
                @if ($barang->is_per_unit)
                    <span class="badge bg-primary">
                        <i class="bi bi-box-seam"></i> Per Unit
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="bi bi-stack"></i> Tidak Per Unit
                    </span>
                @endif
            </td>
            <td class="text-end">
    <div class="btn-group btn-group-sm" role="group">
        @can('manage barang')
            @if ($barang->is_per_unit)
                <a href="{{ route('barang.units.index', $barang->id) }}" 
                    class="btn btn-primary" title="Kelola Unit">
                    <i class="bi bi-box-seam"></i>
                </a>
            @else
                <a href="{{ route('barang.show', $barang->id) }}" 
                    class="btn btn-info" title="Detail">
                    <i class="bi bi-eye"></i>
                </a>
            @endif

            <a href="{{ route('barang.edit', $barang->id) }}" 
                class="btn btn-warning" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
        @endcan

        @can('delete barang')
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-url="{{ route('barang.destroy', $barang->id) }}" title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
        @endcan
    </div>
</td>
        </tr>

        {{-- Collapse Row untuk Detail Unit (hanya jika Per Unit) --}}
        @if ($barang->is_per_unit && $barang->detailBarangs->isNotEmpty())
            @php
                $totalUnit = $barang->detailBarangs->count();
                $unitTersedia = $barang->detailBarangs->where('status', 'Tersedia')->count();
                $unitDipinjam = $barang->detailBarangs->where('status', 'Dipinjam')->count();
            @endphp
            <tr class="table-light">
                <td colspan="11" class="p-0">
                    <button class="btn btn-sm btn-outline-info w-100 text-start" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#units{{ $barang->id }}">
                        <i class="bi bi-chevron-down"></i> Lihat Detail Unit ({{ $totalUnit }})
                    </button>
                </td>
            </tr>

            <tr class="collapse" id="units{{ $barang->id }}">
                <td colspan="11" class="bg-light p-0">
                    <div class="p-3">
                        <h6 class="mb-3">
                            <i class="bi bi-box-seam"></i> Detail Unit - {{ $barang->nama_barang }}
                            <span class="badge bg-success ms-2">{{ $unitTersedia }} Tersedia</span>
                            <span class="badge bg-warning text-dark">{{ $unitDipinjam }} Dipinjam</span>
                        </h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode Unit</th>
                                        <th width="12%">Kondisi</th>
                                        <th width="12%">Status</th>
                                        <th width="25%">Keterangan</th>
                                        <th width="31%">Info Peminjaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barang->detailBarangs as $idx => $detail)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $detail->sub_kode }}</strong>
                                            </td>
                                            <td>
    @php
        // Ambil kondisi langsung dari setiap detail barang (unit)
        $kondisiText = $detail->kondisi;
        $badgeClass = 'bg-success'; // default

        // Jika kondisi null atau kosong → tampilkan strip
        if (is_null($kondisiText) || trim($kondisiText) === '') {
            $kondisiText = '–';
            $badgeClass = 'bg-secondary';
        }
        // Jika rusak ringan
        elseif ($kondisiText === 'Rusak ringan') {
            $badgeClass = 'bg-warning text-dark';
        }
        // Jika rusak berat
        elseif ($kondisiText === 'Rusak berat') {
            $badgeClass = 'bg-danger';
        }
        // Jika baik
        elseif ($kondisiText === 'Baik') {
            $badgeClass = 'bg-success';
        }
    @endphp

    <span class="badge {{ $badgeClass }}">{{ $kondisiText }}</span>
</td>

                                            <td>
                                                @if ($detail->status === 'Tersedia')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Tersedia
                                                    </span>
                                                @elseif ($detail->status === 'Dipinjam')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-clock"></i> Dipinjam
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Rusak
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $detail->keterangan ?? '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $peminjamanAktif = $detail->peminjamanAktif;
                                                @endphp
                                                
                                                @if ($peminjamanAktif)
                                                    <small>
                                                        <i class="bi bi-person-fill text-warning"></i> 
                                                        <strong>{{ $peminjamanAktif->nama_peminjam }}</strong><br>
                                                        <i class="bi bi-calendar-event"></i> 
                                                        Sejak: {{ $peminjamanAktif->tanggal_pinjam->format('d/m/Y') }}
                                                        @if ($peminjamanAktif->kontak)
                                                            <br><i class="bi bi-telephone"></i> {{ $peminjamanAktif->kontak }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        @endif
    @empty
        <tr>
            <td colspan="11" class="text-center">
                <div class="alert alert-danger">
                    Data barang belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>