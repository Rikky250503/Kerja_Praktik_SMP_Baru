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
        Schema::create('tbl_barang', function (Blueprint $table) {
            $table->uuid('id_barang')->primary()->default(DB::raw('UUID()'));
            $table->foreignid('id_kategori')->references('id_kategori')->on('tbl_kategori')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nama_barang');
            $table->integer('kuantitas')->default(0);
            $table->double('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barang');
    }
};
