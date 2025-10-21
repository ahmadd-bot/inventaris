<x-main-layout title-page="Tambah Peminjaman Barang Tidak Per Unit">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Peminjaman Barang Tidak Per Unit</h5>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('peminjaman.storeNotPerUnit') }}">
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
                                <h6 class="mb-0">Pilih Barang & Jumlah</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="barang_id" class="form-label">
                                            Pilih Barang <span class="text-danger">*</span>
                                        </label>
                                        <select name="barang_id" id="barang_id" 
                                            class="form-select @error('barang_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->id }}" data-jumlah="{{ $barang->jumlah }}" data-satuan="{{ $barang->satuan }}">
                                                    {{ $barang->nama_barang }} - Stok: {{ $barang->jumlah }} {{ $barang->satuan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('barang_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah_pinjam" class="form-label">
                                            Jumlah Pinjam <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" 
                                                class="form-control @error('jumlah_pinjam') is-invalid @enderror" 
                                                value="{{ old('jumlah_pinjam', 1) }}" min="1" required>
                                            <span class="input-group-text" id="satuanText">-</span>
                                        </div>
                                        @error('jumlah_pinjam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2" id="stokInfo">Silakan pilih barang terlebih dahulu</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
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
                        <li>Form ini digunakan untuk barang yang bukan per unit (barang dengan stok jumlah)</li>
                        <li>Tentukan jumlah barang yang akan dipinjam</li>
                        <li>Jumlah pinjam tidak boleh melebihi stok yang tersedia</li>
                        <li>Pastikan semua data lengkap sebelum submit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
    const selectBarang = document.getElementById('barang_id');
    const inputJumlah = document.getElementById('jumlah_pinjam');
    const satuanText = document.getElementById('satuanText');
    const stokInfo = document.getElementById('stokInfo');

    selectBarang.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const jumlahStok = parseInt(selectedOption.dataset.jumlah) || 0;
        const satuan = selectedOption.dataset.satuan || '-';

        if (this.value === '') {
            inputJumlah.max = '';
            satuanText.textContent = '-';
            stokInfo.textContent = 'Silakan pilih barang terlebih dahulu';
            inputJumlah.value = 1;
        } else {
            inputJumlah.max = jumlahStok;
            satuanText.textContent = satuan;
            stokInfo.textContent = `Stok tersedia: ${jumlahStok} ${satuan}`;
            inputJumlah.value = 1;
        }
    });

    // Initial trigger
    selectBarang.dispatchEvent(new Event('change'));
    </script>
</x-main-layout>