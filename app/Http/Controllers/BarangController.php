<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DetailBarang;

class BarangController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['index', 'show']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->search;

    $barangs = Barang::with(['kategori', 'lokasi', 'detailBarangs.peminjamanAktif'])
        ->when($search, function ($query, $search) {
            $query->where('nama_barang', 'like', '%' . $search . '%')
                ->orWhere('kode_barang', 'like', '%' . $search . '%')
                ->orWhereHas('lokasi', function ($q) use ($search) {
                    $q->where('nama_lokasi', 'like', '%' . $search . '%');
                })
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', '%' . $search . '%');
                });
        })
        ->latest()
        ->paginate()
        ->withQueryString();

    return view('barang.index', compact('barangs'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        $barang = new Barang();

        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
        'nama_barang' => 'required|string|max:150',
        'kategori_id' => 'required|exists:kategoris,id',
        'lokasi_id' => 'required|exists:lokasis,id',
        'is_per_unit' => 'required|boolean',
        'jumlah' => 'nullable|required_if:is_per_unit,false|integer|min:1',
        'satuan' => 'required|string|max:20',
        'kondisi' => 'nullable|required_if:is_per_unit,false|in:Baik,Rusak ringan,Rusak berat',
        'tanggal_pengadaan' => 'required|date',
        'status_pinjam' => 'required|in:Dapat Dipinjam,Tidak Dapat Dipinjam',
        'sumber_dana' => 'required|in:Pemerintah,Donatur,Swadaya',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Jika per unit, set jumlah ke 0 dan kondisi ke NULL (akan dihitung dari detail unit)
    if ($validated['is_per_unit']) {
        $validated['jumlah'] = 0;
        $validated['kondisi'] = null;  // Kondisi per unit dikelola dari detail_barangs
    }

    if ($request->hasFile('gambar')) {
        $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
    }

    Barang::create($validated);

    $message = $validated['is_per_unit']
        ? 'Data barang berhasil ditambahkan. Gunakan tombol "Kelola Unit" untuk menambahkan detail unit.'
        : 'Data barang berhasil ditambahkan.';

    return redirect()->route('barang.index')->with('success', $message);
}
    /**
     * Display the specified resource.
     * Untuk barang tidak per unit, tampilkan detail barang
     */
    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi']);

        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the existing resource.
     */
    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();

        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
{
    try {
        // Log request data untuk debugging
        \Log::info('Update Barang Request:', [
            'barang_id' => $barang->id,
            'request_data' => $request->all(),
            'is_per_unit_value' => $request->input('is_per_unit'),
            'is_per_unit_type' => gettype($request->input('is_per_unit'))
        ]);

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'is_per_unit' => 'required|boolean',
            'jumlah' => 'nullable|required_if:is_per_unit,false|integer|min:1',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'nullable|required_if:is_per_unit,false|in:Baik,Rusak ringan,Rusak berat',
            'tanggal_pengadaan' => 'required|date',
            'status_pinjam' => 'required|in:Dapat Dipinjam,Tidak Dapat Dipinjam',
            'sumber_dana' => 'required|in:Pemerintah,Donatur,Swadaya',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        \Log::info('Validated Data:', $validated);

        // Jika per unit, set jumlah ke 0 dan kondisi ke Baik
        if ($validated['is_per_unit']) {
            $validated['jumlah'] = 0;
            $validated['kondisi'] = 'Baik';
        }

        // Hapus gambar lama jika ada upload gambar baru
        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('gambar-barang')->delete($barang->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }

        \Log::info('Data sebelum update:', [
            'barang_id' => $barang->id,
            'validated' => $validated
        ]);

        $barang->update($validated);

        \Log::info('Update berhasil untuk barang:', ['barang_id' => $barang->id]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation Error:', $e->errors());
        throw $e;
    } catch (\Exception $e) {
        \Log::error('Update Error:', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
{
    // Cek apakah ada unit yang sedang dipinjam
    $unitDipinjam = $barang->detailBarangs()
        ->where('status', 'Dipinjam')
        ->count();

    if ($unitDipinjam > 0) {
        return back()->with('error', 'Tidak dapat menghapus barang. Masih ada barang yang sedang dipinjam.');
    }

    if ($barang->gambar) {
        Storage::disk('gambar-barang')->delete($barang->gambar);
    }

    // Hapus semua peminjaman barang ini
    Peminjaman::whereIn('detail_barang_id', 
        $barang->detailBarangs()->pluck('id')
    )->delete();

    $barang->detailBarangs()->delete();
    $barang->delete();

    return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
}

    /**
     * Generate PDF laporan barang
     */
    public function cetakLaporan()
    {
        $barangs = Barang::with(['kategori', 'lokasi', 'detailBarangs'])->get();
        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs
        ];

        $pdf = Pdf::loadView('barang.laporan', $data);
        return $pdf->stream('laporan-inventaris-barang.pdf');
    }
}