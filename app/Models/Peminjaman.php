<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';
    
    protected $guarded = ['id'];
    
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function detailBarang(): BelongsTo
    {
        return $this->belongsTo(DetailBarang::class, 'detail_barang_id');
    }
}