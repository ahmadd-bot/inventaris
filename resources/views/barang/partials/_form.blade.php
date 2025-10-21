<!-- Blade code untuk form barang -->
@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Kode Barang" name="kode_barang" :value="$barang->kode_barang" />
    </div>

    <div class="col-md-6">
        <x-form-input label="Nama Barang" name="nama_barang" :value="$barang->nama_barang" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-select label="Kategori" name="kategori_id" :value="$barang->kategori_id"
            :option-data="$kategori" option-label="nama_kategori" option-value="id" />
    </div>

    <div class="col-md-6">
        <x-form-select label="Lokasi" name="lokasi_id" :value="$barang->lokasi_id"
            :option-data="$lokasi" option-label="nama_lokasi" option-value="id" />
    </div>
</div>

{{-- Status Pinjam & Sumber Dana --}}
<div class="row mb-3">
    <div class="col-md-6">
        @php
            $statusPinjam = [
                ['value' => 'Dapat Dipinjam'],
                ['value' => 'Tidak Dapat Dipinjam']
            ];
        @endphp
        <x-form-select label="Status Pinjam" name="status_pinjam" :value="$barang->status_pinjam ?? 'Dapat Dipinjam'" 
            :option-data="$statusPinjam" option-label="value" option-value="value" />
    </div>

    <div class="col-md-6">
        @php
            $sumberDana = [
                ['value' => 'Pemerintah'],
                ['value' => 'Donatur'],
                ['value' => 'Swadaya']
            ];
        @endphp
        <x-form-select label="Sumber Dana" name="sumber_dana" :value="$barang->sumber_dana ?? 'Pemerintah'" 
            :option-data="$sumberDana" option-label="value" option-value="value" />
    </div>
</div>

{{-- Toggle Per Unit / Tidak Per Unit --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Tipe Barang</label>
        <div class="btn-group w-100" role="group">
            <input type="radio" class="btn-check" name="is_per_unit" id="perUnit" value="1" 
                {{ (old('is_per_unit', $barang->is_per_unit) ?? false) ? 'checked' : '' }}>
            <label class="btn btn-outline-primary" for="perUnit">
                <i class="bi bi-box-seam"></i> Per Unit
            </label>

            <input type="radio" class="btn-check" name="is_per_unit" id="tidakPerUnit" value="0" 
                {{ !(old('is_per_unit', $barang->is_per_unit) ?? false) ? 'checked' : '' }}>
            <label class="btn btn-outline-primary" for="tidakPerUnit">
                <i class="bi bi-stack"></i> Tidak Per Unit
            </label>
        </div>
        @error('is_per_unit')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Jumlah & Kondisi (hanya untuk Tidak Per Unit) --}}
<div class="row mb-3" id="notPerUnitSection">
    <div class="col-md-6">
        <x-form-input label="Jumlah" name="jumlah" :value="$barang->jumlah" type="number" 
            placeholder="Masukkan jumlah barang" />
    </div>

    <div class="col-md-6">
        @php
            $kondisi = [['kondisi' => 'Baik'], ['kondisi' => 'Rusak ringan'], ['kondisi' => 'Rusak berat']];
        @endphp
        <x-form-select label="Kondisi" name="kondisi" :value="$barang->kondisi" :option-data="$kondisi"
            option-label="kondisi" option-value="kondisi" />
    </div>
</div>

{{-- Satuan & Tanggal --}}
<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Satuan" name="satuan" :value="$barang->satuan" 
            placeholder="misal: pcs, buah, unit, box, etc" />
    </div>

    <div class="col-md-6">
        @php
            $tanggal = $barang->tanggal_pengadaan
                ? date('Y-m-d', strtotime($barang->tanggal_pengadaan))
                : null;
        @endphp
        <x-form-input label="Tanggal Pengadaan" name="tanggal_pengadaan" type="date" :value="$tanggal" />
    </div>
</div>

{{-- Info untuk Per Unit --}}
<div class="alert alert-info" id="perUnitInfo" style="display: none;">
    <i class="bi bi-info-circle"></i> 
    <strong>Info Per Unit:</strong> Jumlah barang akan otomatis dihitung berdasarkan unit yang ditambahkan di menu <strong>"Kelola Unit"</strong>.
</div>

{{-- Info untuk Tidak Per Unit --}}
<div class="alert alert-info" id="notPerUnitInfo">
    <i class="bi bi-info-circle"></i> 
    <strong>Info Tidak Per Unit:</strong> Barang disimpan berdasarkan jumlah total tanpa perlu membuat unit individual.
</div>

<div class="row mb-3">
    <x-form-input label="Gambar Barang" name="gambar" type="file" />
</div>

<div class="mt-4">
    <x-primary-button>
        {{ isset($update) ? __('Update') : __('Simpan') }}
    </x-primary-button>
    
    <x-tombol-kembali :href="route('barang.index')" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perUnitRadio = document.getElementById('perUnit');
    const tidakPerUnitRadio = document.getElementById('tidakPerUnit');
    const notPerUnitSection = document.getElementById('notPerUnitSection');
    const perUnitInfo = document.getElementById('perUnitInfo');
    const notPerUnitInfo = document.getElementById('notPerUnitInfo');
    const jumlahInput = document.querySelector('input[name="jumlah"]');
    const kondisiSelect = document.querySelector('select[name="kondisi"]');

    function toggleFields() {
        const isPerUnit = perUnitRadio.checked;
        
        // Toggle visibility
        notPerUnitSection.style.display = isPerUnit ? 'none' : 'flex';
        perUnitInfo.style.display = isPerUnit ? 'block' : 'none';
        notPerUnitInfo.style.display = isPerUnit ? 'none' : 'block';

        // Set field visibility dan disable/enable
        if (isPerUnit) {
            // Per Unit: hide jumlah & kondisi, set ke disabled
            jumlahInput.disabled = true;
            jumlahInput.value = '';
            kondisiSelect.disabled = true;
            kondisiSelect.value = '';
        } else {
            // Tidak Per Unit: show dan enable
            jumlahInput.disabled = false;
            kondisiSelect.disabled = false;
        }
    }

    perUnitRadio.addEventListener('change', toggleFields);
    tidakPerUnitRadio.addEventListener('change', toggleFields);

    // Initial state
    toggleFields();
});
</script>