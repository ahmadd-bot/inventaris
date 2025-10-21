<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailBarang extends Model
{
    protected $guarded = ['id'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'detail_barang_id');
    }

    public function peminjamanAktif()
    {
        return $this->hasOne(Peminjaman::class, 'detail_barang_id')
            ->where('status', 'Dipinjam')
            ->latest();
    }
}