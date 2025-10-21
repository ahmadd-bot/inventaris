<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DetailBarangController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }

    /**
     * Halaman kelola unit untuk barang tertentu
     */
    public function index(Barang $barang)
    {
        $barang->load(['detailBarangs.peminjamanAktif', 'kategori', 'lokasi']);
        
        return view('barang.kelola-unit', compact('barang'));
    }

    /**
     * Tambah unit baru
     */
    public function store(Request $request, Barang $barang)
{
    $validated = $request->validate([
        'sub_kode' => 'required|string|max:50|unique:detail_barangs,sub_kode',
        'kondisi' => 'required|in:Baik,Rusak ringan,Rusak berat',
        'status' => 'nullable|in:Tersedia,Rusak',
        'keterangan' => 'nullable|string',
    ]);

    // Jika barang tidak dapat dipinjam, set status ke Tersedia (field di-disable di form)
    // Jika barang dapat dipinjam, status harus diisi dari form
    if ($barang->status_pinjam === 'Tidak Dapat Dipinjam') {
        $validated['status'] = 'Tersedia';
    } else {
        // Validasi ulang jika barang dapat dipinjam
        if (empty($validated['status'])) {
            return back()
                ->withErrors(['status' => 'Status harus diisi'])
                ->withInput();
        }
    }

    $barang->detailBarangs()->create($validated);

    return back()->with('success', 'Unit barang berhasil ditambahkan.');
}


    /**
 * Update unit
 */
public function update(Request $request, Barang $barang, DetailBarang $detailBarang)
{
    // Pastikan detail barang ini milik barang yang dipilih
    if ($detailBarang->barang_id !== $barang->id) {
        return back()->with('error', 'Unit tidak ditemukan.');
    }

    // Cek apakah unit sedang dipinjam - jika ya, tidak bisa diedit
    if ($detailBarang->status === 'Dipinjam') {
        return back()->with('error', 'Unit sedang dipinjam, tidak dapat diedit.');
    }

    $validated = $request->validate([
        'sub_kode' => 'required|string|max:50|unique:detail_barangs,sub_kode,' . $detailBarang->id,
        'kondisi' => 'required|in:Baik,Rusak ringan,Rusak berat',
        'status' => 'nullable|in:Tersedia,Rusak',
        'keterangan' => 'nullable|string',
    ]);

    // Jika barang tidak dapat dipinjam, set status ke Tersedia
    if ($barang->status_pinjam === 'Tidak Dapat Dipinjam') {
        $validated['status'] = 'Tersedia';
    } else {
        // Validasi ulang jika barang dapat dipinjam
        if (empty($validated['status'])) {
            return back()
                ->withErrors(['status' => 'Status harus diisi'])
                ->withInput();
        }
    }

    $detailBarang->update($validated);

    return back()->with('success', 'Unit barang berhasil diperbarui.');
}


    /**
     * Hapus unit
     */
    public function destroy(Barang $barang, DetailBarang $detailBarang)
{
    if ($detailBarang->barang_id !== $barang->id) {
        return back()->with('error', 'Unit tidak ditemukan.');
    }

    if ($detailBarang->status === 'Dipinjam') {
        return back()->with('error', 'Unit sedang dipinjam, tidak dapat dihapus.');
    }

    // Untuk barang tidak per unit, cek apakah masih ada peminjaman
    if (!$detailBarang->barang->is_per_unit) {
        $peminjamanCount = $detailBarang->peminjamans()->count();
        if ($peminjamanCount > 0) {
            return back()->with('error', 'Masih ada riwayat peminjaman untuk unit ini. Tidak dapat dihapus.');
        }
    } else {
        // Untuk barang per unit, hapus semua peminjaman
        $detailBarang->peminjamans()->delete();
    }

    $detailBarang->delete();

    return back()->with('success', 'Unit barang berhasil dihapus.');
}

   public function storeBulk(Request $request, Barang $barang)
{
    $validated = $request->validate([
        'jumlah_unit' => 'required|integer|min:1|max:500',
        'kode_mulai' => 'required|string|max:10',
        'kondisi' => 'required|in:Baik,Rusak ringan,Rusak berat',
        'status' => 'nullable|in:Tersedia,Rusak',  // ✅ Hapus 'Dipinjam'
    ]);

    // Jika barang tidak dapat dipinjam, set status ke Tersedia
    if ($barang->status_pinjam === 'Tidak Dapat Dipinjam') {
        $validated['status'] = 'Tersedia';
    } else {
        // Validasi ulang jika barang dapat dipinjam
        if (empty($validated['status'])) {
            return redirect()->back()
                ->withErrors(['status' => 'Status harus diisi'])
                ->withInput();
        }
    }

    $jumlahUnit = $validated['jumlah_unit'];
    $kodeMulai = intval($validated['kode_mulai']);
    $kodeBarang = $barang->kode_barang;
    
    $unitsToCreate = [];
    
    for ($i = 0; $i < $jumlahUnit; $i++) {
        $nomorUrut = str_pad($kodeMulai + $i, 3, '0', STR_PAD_LEFT);
        $subKode = $kodeBarang . '-' . $nomorUrut;
        
        // Cek apakah kode sudah ada
        if (DetailBarang::where('sub_kode', $subKode)->exists()) {
            return redirect()->back()
                ->withErrors(['kode_mulai' => "Kode unit $subKode sudah ada. Gunakan nomor mulai yang berbeda."])
                ->withInput();
        }
        
        $unitsToCreate[] = [
            'barang_id' => $barang->id,
            'sub_kode' => $subKode,
            'kondisi' => $validated['kondisi'],
            'status' => $validated['status'],
            'keterangan' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    // Insert semua unit sekaligus
    DetailBarang::insert($unitsToCreate);
    
    return redirect()->route('barang.units.index', $barang)
        ->with('success', "Berhasil menambahkan $jumlahUnit unit baru.");
}

    /**
     * Display the specified unit detail.
     */
    public function show(Barang $barang, DetailBarang $detailBarang)
    {
        // Load relasi yang dibutuhkan
        $detailBarang->load(['barang.kategori', 'barang.lokasi', 'peminjamanAktif']);
        
        return view('barang.show-unit', compact('barang', 'detailBarang'));
    }
}