<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->string('sub_kode', 50)->unique(); // Contoh: LPT-001, LPT-002
            $table->enum('kondisi', ['Baik', 'Rusak ringan', 'Rusak berat'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Rusak'])->default('Tersedia');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barangs');
    }
};