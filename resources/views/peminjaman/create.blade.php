<x-main-layout title-page="Tambah Peminjaman Barang">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Peminjaman Barang (Per Unit)</h5>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('peminjaman.store') }}" id="formPeminjaman">
                        @csrf

                        {{-- Section: Data Peminjam --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Data Peminjam</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_peminjam" class="form-label">
                                                Nama Peminjam <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="nama_peminjam" id="nama_peminjam" 
                                                class="form-control @error('nama_peminjam') is-invalid @enderror" 
                                                value="{{ old('nama_peminjam') }}" required>
                                            @error('nama_peminjam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kontak" class="form-label">Kontak (HP/Email)</label>
                                            <input type="text" name="kontak" id="kontak" 
                                                class="form-control @error('kontak') is-invalid @enderror" 
                                                value="{{ old('kontak') }}" 
                                                placeholder="08xxxxxxxxxx / email@example.com">
                                            @error('kontak')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="keperluan" class="form-label">Keperluan</label>
                                            <textarea name="keperluan" id="keperluan" rows="2" 
                                                class="form-control @error('keperluan') is-invalid @enderror" 
                                                placeholder="Jelaskan keperluan peminjaman...">{{ old('keperluan') }}</textarea>
                                            @error('keperluan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_pinjam" class="form-label">
                                                Tanggal Pinjam <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" 
                                                class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                                value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                                            @error('tanggal_pinjam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan</label>
                                    <textarea name="catatan" id="catatan" rows="2" 
                                        class="form-control @error('catatan') is-invalid @enderror" 
                                        placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section: Pilih Barang --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Pilih Unit Barang</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="detail_barang_id" class="form-label">
                                            Pilih Unit <span class="text-danger">*</span>
                                        </label>
                                        <select id="detail_barang_id" 
                                            class="form-select" required>
                                            <option value="">-- Pilih Unit --</option>
                                            @foreach ($barangs as $barang)
                                                @if ($barang->is_per_unit && $barang->detailBarangs)
                                                    <optgroup label="{{ $barang->nama_barang }}">
                                                        @foreach ($barang->detailBarangs as $detail)
                                                            <option value="{{ $detail->id }}" data-barang="{{ $barang->nama_barang }}" data-kondisi="{{ $detail->kondisi }}">
                                                                {{ $detail->sub_kode }} - {{ $detail->kondisi }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="btnAddItem">
                                            <i class="bi bi-plus-circle"></i> Tambah Unit
                                        </button>
                                    </div>
                                </div>

                                {{-- Table Daftar Unit yang Dipilih --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="tabelItems">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="20%">Kode Unit</th>
                                                <th width="35%">Nama Barang</th>
                                                <th width="20%">Kondisi</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                            <tr id="emptyRow">
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    Belum ada unit yang dipilih
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden input untuk store data items --}}
                        <input type="hidden" name="items_json" id="items_json" value="[]">

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                                <i class="bi bi-save"></i> Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card mt-3">
                <div class="card-body bg-light">
                    <h6 class="mb-2"><i class="bi bi-info-circle"></i> Informasi</h6>
                    <ul class="mb-0 small">
                        <li>Pilih unit barang yang akan dipinjam satu per satu</li>
                        <li>Klik "Tambah Unit" untuk menambahkan ke daftar</li>
                        <li>Anda bisa tambah hingga 20+ unit dalam 1 form peminjaman</li>
                        <li>Data peminjam (nama, kontak, keperluan) sama untuk semua unit</li>
                        <li>Pastikan semua data lengkap sebelum submit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
    let items = [];
    let barangsData = {!! json_encode($barangs->map(function($b) { return ['id' => $b->id, 'nama_barang' => $b->nama_barang]; })->keyBy('id')) !!};

    const selectBarang = document.getElementById('detail_barang_id');
    const btnAddItem = document.getElementById('btnAddItem');
    const itemsBody = document.getElementById('itemsBody');
    const emptyRow = document.getElementById('emptyRow');
    const formPeminjaman = document.getElementById('formPeminjaman');
    const inputItemsJson = document.getElementById('items_json');
    const btnSubmit = document.getElementById('btnSubmit');

    btnAddItem.addEventListener('click', function() {
        const selectedOption = selectBarang.options[selectBarang.selectedIndex];
        
        if (!selectBarang.value) {
            alert('Silakan pilih unit barang terlebih dahulu');
            return;
        }

        const detailId = parseInt(selectBarang.value);
        
        // Cek apakah unit sudah ditambahkan
        if (items.some(item => item.detail_id === detailId)) {
            alert('Unit ini sudah ditambahkan ke daftar');
            return;
        }

        const item = {
            detail_id: detailId,
            kode_unit: selectedOption.textContent.split(' - ')[0],
            nama_barang: selectedOption.dataset.barang,
            kondisi: selectedOption.dataset.kondisi
        };

        items.push(item);
        renderItems();
        selectBarang.value = '';
    });

    function renderItems() {
        itemsBody.innerHTML = '';
        
        if (items.length === 0) {
            itemsBody.appendChild(emptyRow);
            btnSubmit.disabled = true;
        } else {
            emptyRow.style.display = 'none';
            btnSubmit.disabled = false;
            
            items.forEach((item, idx) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${idx + 1}</td>
                    <td><strong class="text-primary">${item.kode_unit}</strong></td>
                    <td>${item.nama_barang}</td>
                    <td><span class="badge bg-success">${item.kondisi}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${idx})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                itemsBody.appendChild(row);
            });
        }

        // Update hidden input
        inputItemsJson.value = JSON.stringify(items.map(item => item.detail_id));
    }

    function removeItem(idx) {
        items.splice(idx, 1);
        renderItems();
    }

    formPeminjaman.addEventListener('submit', function(e) {
        if (items.length === 0) {
            e.preventDefault();
            alert('Silakan tambahkan minimal 1 unit barang');
        }
    });

    // Initial render
    renderItems();
    </script>
</x-main-layout>