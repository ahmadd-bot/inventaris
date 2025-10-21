<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $peminjamans = Peminjaman::with(['detailBarang.barang'])
            ->when($search, function ($query, $search) {
                $query->where('nama_peminjam', 'like', '%' . $search . '%')
                    ->orWhereHas('detailBarang', function ($q) use ($search) {
                        $q->where('sub_kode', 'like', '%' . $search . '%')
                            ->orWhereHas('barang', function ($q2) use ($search) {
                                $q2->where('nama_barang', 'like', '%' . $search . '%');
                            });
                    });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate()
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Show the form for creating a new resource (PER UNIT).
     */
    public function create()
    {
        $barangs = Barang::where('status_pinjam', 'Dapat Dipinjam')
            ->where('is_per_unit', true)
            ->whereHas('detailBarangs', function ($query) {
                $query->where('status', 'Tersedia');
            })
            ->with(['detailBarangs' => function ($query) {
                $query->where('status', 'Tersedia');
            }])
            ->get();

        $peminjaman = new Peminjaman();

        return view('peminjaman.create', compact('peminjaman', 'barangs'));
    }

    /**
     * Show the form for creating a new resource (TIDAK PER UNIT).
     */
    public function createNotPerUnit()
    {
        $barangs = Barang::where('status_pinjam', 'Dapat Dipinjam')
            ->where('is_per_unit', false)
            ->where('jumlah', '>', 0)
            ->get();

        $peminjaman = new Peminjaman();

        return view('peminjaman.create-not-per-unit', compact('peminjaman', 'barangs'));
    }

    /**
     * Store a newly created resource in storage (PER UNIT - MULTI ITEM).
     */
    /**
 * Store a newly created resource in storage (PER UNIT - MULTI ITEM).
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'nama_peminjam' => 'required|string|max:150',
        'kontak' => 'nullable|string|max:50',
        'keperluan' => 'nullable|string',
        'tanggal_pinjam' => 'required|date',
        'catatan' => 'nullable|string',
        'items_json' => 'required|json',
    ]);

    $itemIds = json_decode($validated['items_json'], true);

    if (empty($itemIds)) {
        return back()->with('error', 'Minimal harus ada 1 unit yang dipilih.');
    }

    // Validasi semua unit
    foreach ($itemIds as $detailId) {
        $detail = DetailBarang::find($detailId);
        
        if (!$detail) {
            return back()->with('error', 'Unit tidak ditemukan.');
        }

        if ($detail->status !== 'Tersedia') {
            return back()->with('error', 'Unit ' . $detail->sub_kode . ' tidak tersedia untuk dipinjam.');
        }

        if ($detail->barang->status_pinjam !== 'Dapat Dipinjam') {
            return back()->with('error', 'Barang ' . $detail->barang->nama_barang . ' tidak dapat dipinjam.');
        }
    }

    // Buat peminjaman untuk setiap unit
    $createdCount = 0;
    foreach ($itemIds as $detailId) {
        $detail = DetailBarang::find($detailId);

        Peminjaman::create([
            'detail_barang_id' => $detailId,
            'nama_peminjam' => $validated['nama_peminjam'],
            'kontak' => $validated['kontak'],
            'keperluan' => $validated['keperluan'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'jumlah_pinjam' => 1,
            'status' => 'Dipinjam',
            'catatan' => $validated['catatan'],
        ]);

        // ✅ JANGAN update status detail_barang - status Dipinjam ditrack dari tabel peminjamans
        // $detail->update(['status' => 'Dipinjam']); // ❌ HAPUS INI
        
        $createdCount++;
    }

    return redirect()->route('peminjaman.index')
        ->with('success', "Data peminjaman berhasil ditambahkan untuk $createdCount unit.");
}

    /**
 * Store a newly created resource in storage (TIDAK PER UNIT - SINGLE ITEM).
 */
public function storeNotPerUnit(Request $request)
{
    $validated = $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'nama_peminjam' => 'required|string|max:150',
        'kontak' => 'nullable|string|max:50',
        'keperluan' => 'nullable|string',
        'tanggal_pinjam' => 'required|date',
        'jumlah_pinjam' => 'required|integer|min:1',
        'catatan' => 'nullable|string',
    ]);

    $barang = Barang::findOrFail($validated['barang_id']);

    // Validasi barang
    if ($barang->is_per_unit) {
        return back()->with('error', 'Barang ini adalah barang per unit. Gunakan form "Pinjam Per Unit".');
    }

    if ($barang->status_pinjam !== 'Dapat Dipinjam') {
        return back()->with('error', 'Barang ini tidak dapat dipinjam.');
    }

    if ($validated['jumlah_pinjam'] > $barang->jumlah) {
        return back()->with('error', 'Jumlah pinjam melebihi stok yang tersedia.');
    }

    // Buat temporary detail_barang jika belum ada
    $detailBarang = DetailBarang::firstOrCreate(
        ['barang_id' => $barang->id, 'sub_kode' => $barang->kode_barang . '-TEMP'],
        [
            'lokasi_id' => $barang->lokasi_id,
            'kondisi' => $barang->kondisi,
            'status' => 'Tersedia',
            'keterangan' => 'Temporary unit untuk barang tidak per unit'
        ]
    );

    // Buat peminjaman
    Peminjaman::create([
        'detail_barang_id' => $detailBarang->id,
        'nama_peminjam' => $validated['nama_peminjam'],
        'kontak' => $validated['kontak'],
        'keperluan' => $validated['keperluan'],
        'tanggal_pinjam' => $validated['tanggal_pinjam'],
        'jumlah_pinjam' => $validated['jumlah_pinjam'],
        'status' => 'Dipinjam',
        'catatan' => $validated['catatan'],
    ]);

    // ✅ JANGAN update status detail_barang - status Dipinjam ditrack dari tabel peminjamans
    // $detailBarang->update(['status' => 'Dipinjam']); // ❌ HAPUS INI

    // Kurangi stok barang
    $barang->decrement('jumlah', $validated['jumlah_pinjam']);

    return redirect()->route('peminjaman.index')
        ->with('success', 'Data peminjaman berhasil ditambahkan.');
}

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['detailBarang.barang']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Kembalikan barang yang dipinjam
     */
    public function kembalikan(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dikembalikan') {
            return back()->with('error', 'Barang sudah dikembalikan.');
        }

        $validated = $request->validate([
            'tanggal_kembali' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['status'] = 'Dikembalikan';

        // Update peminjaman
        $peminjaman->update($validated);

        // Update status detail barang
        $peminjaman->detailBarang->update(['status' => 'Tersedia']);

        // Untuk barang tidak per unit, tambah stok kembali
        $barang = $peminjaman->detailBarang->barang;
        if (!$barang->is_per_unit) {
            $barang->increment('jumlah', $peminjaman->jumlah_pinjam);
        }

        return redirect()->route('peminjaman.index')
            ->with('success', 'Barang berhasil dikembalikan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status === 'Dipinjam') {
            $peminjaman->detailBarang->update(['status' => 'Tersedia']);

            $barang = $peminjaman->detailBarang->barang;
            if (!$barang->is_per_unit) {
                $barang->increment('jumlah', $peminjaman->jumlah_pinjam);
            }
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }

    /**
     * Generate PDF laporan peminjaman
     */
    public function cetakLaporan(Request $request)
    {
        $status = $request->status ?? 'all';
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;

        $query = Peminjaman::with(['detailBarang.barang']);

        // Filter berdasarkan status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan range tanggal
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('tanggal_pinjam', [$tanggalMulai, $tanggalAkhir]);
        } elseif ($tanggalMulai) {
            $query->whereDate('tanggal_pinjam', '>=', $tanggalMulai);
        } elseif ($tanggalAkhir) {
            $query->whereDate('tanggal_pinjam', '<=', $tanggalAkhir);
        }

        $peminjamans = $query->latest()->get();

        $data = [
            'title' => 'Laporan Data Peminjaman Barang',
            'date' => date('d F Y'),
            'peminjamans' => $peminjamans,
            'status' => $status,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('peminjaman.laporan', $data);
        return $pdf->stream('laporan-peminjaman-barang.pdf');
    }
}