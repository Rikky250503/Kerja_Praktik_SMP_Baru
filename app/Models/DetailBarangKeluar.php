<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DetailBarangKeluar extends Model
{
    use HasFactory,HasUuids;

    protected $table = 'tbl_detail_barang_keluar';
    protected $primaryKey = 'id_detail_barang_keluar';
    protected $fillable = ['id_barang_keluar','id_barang','kuantitas','harga_barang_keluar','total'];

    public function barangkeluar(): BelongsTo
    {
        return $this->belongsTo(BarangKeluar::class,'id_barang_keluar');
    }
    public function barang(): HasOne
    {
        return $this->hasOne(Barang::class,'id_barang');
    }
}
