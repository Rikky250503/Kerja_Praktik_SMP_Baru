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
        Schema::create('tbl_barangkeluar', function (Blueprint $table) {
            $table->uuid('id_barang_keluar')->primary();
            $table->date('tanggal_keluar');
            $table->string('nomor_invoice_keluar');
            $table->double('total')->default(0);
            $table->foreignUuid('id_customer')->nullable()->references('id_customer')->on('tbl_customer')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignid('id_status')->default('1')->references('id_status')->on('tbl_status')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('created_by')->nullable()->references('id_user')->on('tbl_useradmin')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barangkeluar');
    }
};
