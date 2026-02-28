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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 20)->unique()->comment('Kode unik barang');
            $table->string('nama_barang', 150)->comment('Nama barang/produk');
            $table->string('kategori', 100)->comment('Kategori: minyak_pijat, handuk, lotion, alat, dsb');
            $table->string('satuan', 30)->default('pcs')->comment('Satuan: pcs, botol, lembar, ml, dll');
            $table->integer('stok_awal')->default(0)->comment('Stok awal saat input pertama kali');
            $table->integer('stok_masuk')->default(0)->comment('Total stok yang masuk/ditambahkan');
            $table->integer('stok_keluar')->default(0)->comment('Total stok yang keluar/terpakai');
            $table->integer('stok_aktual')->default(0)->comment('Stok fisik hasil hitung manual/kroscek');
            $table->integer('stok_sistem')->virtualAs('stok_awal + stok_masuk - stok_keluar')->comment('Stok sistem (otomatis hitung)');
            $table->integer('selisih')->virtualAs('stok_aktual - (stok_awal + stok_masuk - stok_keluar)')->comment('Selisih stok aktual vs sistem');
            $table->decimal('harga_beli', 12, 2)->default(0)->comment('Harga beli per satuan');
            $table->decimal('harga_jual', 12, 2)->default(0)->comment('Harga jual/pakai per satuan');
            $table->integer('stok_minimum')->default(5)->comment('Batas minimum stok sebelum peringatan');
            $table->string('lokasi_simpan', 100)->nullable()->comment('Lokasi penyimpanan barang, misal: Lemari A, Rak 1');
            $table->date('tanggal_kadaluarsa')->nullable()->comment('Tanggal kadaluarsa (jika ada)');
            $table->date('tanggal_kroscek')->nullable()->comment('Tanggal kroscek/pengecekan stok terakhir');
            $table->string('petugas_kroscek', 100)->nullable()->comment('Nama petugas yang melakukan kroscek');
            $table->text('catatan')->nullable()->comment('Catatan tambahan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index('kategori');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
