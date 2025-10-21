<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_barangs')->insert([
            // Unit untuk Laptop Dell Latitude 5420 (barang_id: 1)
            [
                'barang_id' => 1,
                'sub_kode' => 'LP001-001',
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Unit 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barang_id' => 1,
                'sub_kode' => 'LP001-002',
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Unit 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barang_id' => 1,
                'sub_kode' => 'LP001-003',
                'kondisi' => 'Rusak ringan',
                'status' => 'Rusak',
                'keterangan' => 'Unit 3 - Layar bergaris',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Unit untuk Proyektor Epson EB-X500 (barang_id: 2)
            [
                'barang_id' => 2,
                'sub_kode' => 'PRJ01-001',
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Unit 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barang_id' => 2,
                'sub_kode' => 'PRJ01-002',
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Unit 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Unit untuk Meja Rapat Kayu Jati (barang_id: 3)
            [
                'barang_id' => 3,
                'sub_kode' => 'MJ005-001',
                'kondisi' => 'Baik',
                'status' => 'Tersedia',
                'keterangan' => 'Meja utama ruang rapat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}