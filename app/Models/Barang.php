<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Barang extends Model
{
    use HasFactory, HasUuids;

    protected $table = "tbl_barang";
    protected $primaryKey = 'id_barang';
    protected $fillable = ['nama_barang','kuantitas','harga','id_kategori'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class,'id_kategori','id_kategori');
    }
    public function detailbarangkeluar(): HasOne
    {
        return $this->hasOne(DetailBarangKeluar::class,'id_barang');
    }
    public function detailbarangmasuk(): HasOne
    {
        return $this->hasOne(DetailBarangMasuk::class,'id_barang');
    }
}
