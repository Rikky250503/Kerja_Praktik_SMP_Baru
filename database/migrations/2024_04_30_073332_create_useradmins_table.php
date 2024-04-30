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
        Schema::create('tbl_useradmin', function (Blueprint $table) {
            $table->uuid('id_user')->primary();
            $table->string('username_user');
            $table->string('password_user');
            $table->string('nama_user');
            $table->enum('jabatan_user',['P','J','G'])->default('G');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_useradmin');
    }
};
