<x-main-layout title-page="Kelola Unit Barang">
    {{-- Info Barang --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2">
                        <i class="bi bi-box"></i> {{ $barang->nama_barang }}
                    </h5>
                    <div class="text-muted">
                        <span class="me-3"><strong>Kode:</strong> {{ $barang->kode_barang }}</span>
                        <span class="me-3"><strong>Kategori:</strong> {{ $barang->kategori->nama_kategori }}</span>
                        <span class="me-3">
                            <strong>Status Pinjam:</strong> 
                            @php
                                $pinjamBadge = $barang->status_pinjam === 'Dapat Dipinjam' ? 'bg-success' : 'bg-danger';
                            @endphp
                            <span class="badge {{ $pinjamBadge }}">{{ $barang->status_pinjam }}</span>
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <x-tombol-kembali :href="route('barang.index')" />
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    <x-notif-alert class="mb-4" />

    {{-- Alert jika barang tidak dapat dipinjam --}}
    @if ($barang->status_pinjam === 'Tidak Dapat Dipinjam')
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Perhatian!</strong> Barang ini di-set <strong>"Tidak Dapat Dipinjam"</strong>, sehingga field peminjaman di bawah tidak tersedia.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Tambah Unit Baru --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Unit Baru</h6>
        </div>
        <div class="card-body">
            {{-- Tab Navigation --}}
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="single-tab" data-bs-toggle="tab" 
                        data-bs-target="#single-unit" type="button" role="tab">
                        <i class="bi bi-file-plus"></i> Tambah 1 Unit
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" 
                        data-bs-target="#bulk-unit" type="button" role="tab">
                        <i class="bi bi-files"></i> Tambah Banyak Unit
                    </button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- Single Unit Form --}}
                <div class="tab-pane fade show active" id="single-unit" role="tabpanel">
                    <form method="POST" action="{{ route('barang.units.store', $barang) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <x-form-input label="Kode Unit" name="sub_kode" 
                                    placeholder="Contoh: {{ $barang->kode_barang }}-001" required />
                                <small class="text-muted">Format: {{ $barang->kode_barang }}-XXX</small>
                            </div>
                            <div class="col-md-3">
                                @php
                                    $kondisiOptions = [
                                        ['kondisi' => 'Baik'], 
                                        ['kondisi' => 'Rusak ringan'], 
                                        ['kondisi' => 'Rusak berat']
                                    ];
                                @endphp
                                <x-form-select label="Kondisi" name="kondisi" :option-data="$kondisiOptions" 
                                    option-label="kondisi" option-value="kondisi" value="Baik" required />
                            </div>
                            <div class="col-md-3">
                                @php
                                    $statusOptions = [
                                        ['status' => 'Tersedia'], 
                                        ['status' => 'Rusak']
                                    ];
                                @endphp
                                <x-form-select label="Status" name="status" :option-data="$statusOptions" 
                                    option-label="status" option-value="status" value="Tersedia" required 
                                    :disabled="$barang->status_pinjam === 'Tidak Dapat Dipinjam'" />
                                @if ($barang->status_pinjam === 'Tidak Dapat Dipinjam')
                                    <small class="text-danger d-block mt-1">Status otomatis: Tersedia (barang tidak dapat dipinjam)</small>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <div class="input-group">
                                    <input type="text" name="keterangan" class="form-control" 
                                        placeholder="Keterangan...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Bulk Unit Form --}}
                <div class="tab-pane fade" id="bulk-unit" role="tabpanel">
                    <form method="POST" action="{{ route('barang.units.storeBulk', $barang) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Jumlah Unit <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_unit" class="form-control" 
                                    placeholder="Contoh: 50" min="1" max="500" required>
                                <small class="text-muted">Maksimal 500 unit sekaligus</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kode Mulai <span class="text-danger">*</span></label>
                                <input type="text" name="kode_mulai" class="form-control" 
                                    placeholder="Contoh: 001" required>
                                <small class="text-muted">Nomor awal urutan</small>
                            </div>
                            <div class="col-md-2">
                                @php
                                    $kondisiOptions = [
                                        ['kondisi' => 'Baik'], 
                                        ['kondisi' => 'Rusak ringan'], 
                                        ['kondisi' => 'Rusak berat']
                                    ];
                                @endphp
                                <x-form-select label="Kondisi" name="kondisi" :option-data="$kondisiOptions" 
                                    option-label="kondisi" option-value="kondisi" value="Baik" required />
                            </div>
                            <div class="col-md-2">
                                @php
                                    $statusOptions = [
                                        ['status' => 'Tersedia'], 
                                        ['status' => 'Rusak']
                                    ];
                                @endphp
                                <x-form-select label="Status" name="status" :option-data="$statusOptions" 
                                    option-label="status" option-value="status" value="Tersedia" required 
                                    :disabled="$barang->status_pinjam === 'Tidak Dapat Dipinjam'" />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle"></i> Tambah Massal
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Contoh:</strong> Jika jumlah = 50 dan kode mulai = 004, maka akan dibuat unit: 
                            <code>{{ $barang->kode_barang }}-004</code> sampai 
                            <code>{{ $barang->kode_barang }}-053</code>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Unit --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-list-ul"></i> Daftar Unit ({{ $barang->detailBarangs->count() }} Unit)
            </h6>
            <div>
                @php
                    $tersedia = $barang->detailBarangs->where('status', 'Tersedia')->count();
                    $dipinjam = $barang->detailBarangs->where('status', 'Dipinjam')->count();
                    $rusak = $barang->detailBarangs->where('status', 'Rusak')->count();
                @endphp
                <span class="badge bg-success">{{ $tersedia }} Tersedia</span>
                @if ($barang->status_pinjam === 'Dapat Dipinjam')
                    <span class="badge bg-warning text-dark">{{ $dipinjam }} Dipinjam</span>
                @endif
                <span class="badge bg-danger">{{ $rusak }} Rusak</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($barang->detailBarangs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Unit</th>
                                <th width="12%">Kondisi</th>
                                <th width="12%">Status</th>
                                <th width="25%">Keterangan</th>
                                @if ($barang->status_pinjam === 'Dapat Dipinjam')
                                    <th width="20%">Info Peminjaman</th>
                                @endif
                                <th width="11%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barang->detailBarangs as $index => $unit)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $unit->sub_kode }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-success';
                                            if ($unit->kondisi == 'Rusak ringan') $badgeClass = 'bg-warning text-dark';
                                            if ($unit->kondisi == 'Rusak berat') $badgeClass = 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $unit->kondisi }}</span>
                                    </td>
                                    <td>
                                        @if ($unit->status === 'Tersedia')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Tersedia
                                            </span>
                                        @elseif ($unit->status === 'Dipinjam')
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
                                        <small class="text-muted">{{ $unit->keterangan ?? '-' }}</small>
                                    </td>
                                    @if ($barang->status_pinjam === 'Dapat Dipinjam')
                                        <td>
                                            @if ($unit->peminjamanAktif)
                                                <small>
                                                    <i class="bi bi-person-fill text-warning"></i> 
                                                    <strong>{{ $unit->peminjamanAktif->nama_peminjam }}</strong><br>
                                                    <i class="bi bi-calendar-event"></i> 
                                                    {{ $unit->peminjamanAktif->tanggal_pinjam->format('d/m/Y') }}
                                                </small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <x-tombol-aksi href="{{ route('barang.units.show', [$barang, $unit]) }}" type="show" />
                                        
                                        @if ($unit->status !== 'Dipinjam')
    <button type="button" class="btn btn-sm btn-warning" 
        data-bs-toggle="modal" data-bs-target="#editModal{{ $unit->id }}">
        <i class="bi bi-pencil"></i>
    </button>
@else
    <button type="button" class="btn btn-sm btn-secondary" disabled 
        title="Unit sedang dipinjam, tidak dapat diedit">
        <i class="bi bi-pencil"></i>
    </button>
@endif
                                        
                                        @can('delete barang')
                                            @if ($unit->status !== 'Dipinjam')
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-url="{{ route('barang.units.destroy', [$barang, $unit]) }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
<div class="modal fade" id="editModal{{ $unit->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('barang.units.update', [$barang, $unit]) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Unit: {{ $unit->sub_kode }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Unit <span class="text-danger">*</span></label>
                        <input type="text" name="sub_kode" class="form-control" 
                            value="{{ $unit->sub_kode }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select" required>
                            <option value="Baik" {{ $unit->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak ringan" {{ $unit->kondisi == 'Rusak ringan' ? 'selected' : '' }}>Rusak ringan</option>
                            <option value="Rusak berat" {{ $unit->kondisi == 'Rusak berat' ? 'selected' : '' }}>Rusak berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Tersedia" {{ $unit->status == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Rusak" {{ $unit->status == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> 
                            Status "Dipinjam" hanya dapat diset melalui fitur Peminjaman
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $unit->keterangan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Belum ada unit untuk barang ini. Tambahkan unit baru di atas.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Delete --}}
    <x-modal-delete />
</x-main-layout>