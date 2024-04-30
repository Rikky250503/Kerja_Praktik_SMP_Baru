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
        Schema::create('tbl_detail_barang_keluar', function (Blueprint $table) {
            $table->uuid('id_detail_barang_keluar')->primary();
            $table->foreignUuid('id_barang_keluar')->references('id_barang_keluar')->on('tbl_barangkeluar')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('id_barang')->references('id_barang')->on('tbl_barang')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('kuantitas');
            $table->double('harga_barang_keluar');
            $table->double('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_detail_barang_keluar');
    }
};
