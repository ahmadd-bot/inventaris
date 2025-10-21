<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'lokasi_id',
        'is_per_unit',
        'jumlah',
        'satuan',
        'kondisi',
        'tanggal_pengadaan',
        'status_pinjam',
        'sumber_dana',
        'gambar',
    ];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
        'is_per_unit' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function detailBarangs(): HasMany
    {
        return $this->hasMany(DetailBarang::class, 'barang_id');
    }

    /**
     * Accessor untuk menghitung jumlah barang
     * Jika is_per_unit = true, hitung dari detail_barangs
     * Jika is_per_unit = false, gunakan kolom jumlah langsung
     */
    protected function jumlahAkhir(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_per_unit ? $this->detailBarangs->count() : $this->jumlah
        );
    }

    /**
     * Accessor untuk menghitung jumlah unit (hanya untuk barang per unit)
     */
    protected function jumlahUnit(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->detailBarangs->count()
        );
    }

    /**
     * Accessor untuk mendapatkan kondisi dominan dari unit-unit
     * Jika is_per_unit = true, ambil kondisi dominan dari detail unit
     * Jika is_per_unit = false, gunakan kondisi barang langsung
     */
    protected function kondisiDominan(): Attribute
    {
        return Attribute::make(
            get: function() {
                // Barang TIDAK per unit → ambil dari kolom kondisi langsung
                if (!$this->is_per_unit) {
                    return $this->kondisi ?? '-';
                }

                // Barang PER UNIT → ambil dari detail unit
                $kondisi = $this->detailBarangs->pluck('kondisi');

                // Jika belum ada detail unit → tampilkan strip
                if ($kondisi->isEmpty()) {
                    return '-';
                }

                // Hitung kondisi dominan
                $counts = $kondisi->countBy();
                return $counts->sortDesc()->keys()->first();
            }
        );
    }

    /**
     * Method helper untuk cek apakah barang per unit atau tidak
     */
    public function isPerUnit(): bool
    {
        return (bool) $this->is_per_unit;
    }

    public function isTidakPerUnit(): bool
    {
        return !$this->is_per_unit;
    }
}