<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangs')->insert([
            // Elektronik - Per Unit (kondisi dikosongkan)
            [
                'kode_barang' => 'LP001',
                'nama_barang' => 'Laptop Dell Latitude 5420',
                'kategori_id' => 1,
                'lokasi_id' => 4,
                'jumlah' => 0,
                'satuan' => 'Unit',
                'kondisi' => null, // dikosongkan
                'is_per_unit' => true,
                'status_pinjam' => 'Dapat Dipinjam',
                'sumber_dana' => 'Pemerintah',
                'tanggal_pengadaan' => '2023-05-15',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Elektronik - Per Unit (kondisi dikosongkan)
            [
                'kode_barang' => 'PRJ01',
                'nama_barang' => 'Proyektor Epson EB-X500',
                'kategori_id' => 1,
                'lokasi_id' => 1,
                'jumlah' => 0,
                'satuan' => 'Unit',
                'kondisi' => null, // dikosongkan
                'is_per_unit' => true,
                'status_pinjam' => 'Dapat Dipinjam',
                'sumber_dana' => 'Pemerintah',
                'tanggal_pengadaan' => '2022-11-20',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Furniture - Per Unit (kondisi dikosongkan & tidak dapat dipinjam)
            [
                'kode_barang' => 'MJ005',
                'nama_barang' => 'Meja Rapat Kayu Jati',
                'kategori_id' => 2,
                'lokasi_id' => 1,
                'jumlah' => 0,
                'satuan' => 'Buah',
                'kondisi' => null, // dikosongkan
                'is_per_unit' => true,
                'status_pinjam' => 'Tidak Dapat Dipinjam',
                'sumber_dana' => 'Pemerintah',
                'tanggal_pengadaan' => '2022-02-10',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Alat Tulis - Tidak Per Unit (kondisi tetap)
            [
                'kode_barang' => 'ATK-SP-01',
                'nama_barang' => 'Spidol Whiteboard Snowman',
                'kategori_id' => 3,
                'lokasi_id' => 3,
                'jumlah' => 50,
                'satuan' => 'PCS',
                'kondisi' => 'Baik', // tetap diisi
                'is_per_unit' => false,
                'status_pinjam' => 'Dapat Dipinjam',
                'sumber_dana' => 'Pemerintah',
                'tanggal_pengadaan' => '2024-01-30',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
