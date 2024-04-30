<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBarangMasuk extends Model
{
    use HasFactory,HasUuids;

    protected $table = "tbl_detail_barang_masuk";
    protected $primaryKey = 'id_detail_barang_masuk';
    protected $fillable = ['id_barang','id_barang_masuk','kuantitas','harga_satuan','total'];

    public function barangmasuk(): BelongsTo
    {
        return $this->belongsTo(Barangmasuk::class,'id_barang_masuk');
    }
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class,'id_barang');
    }
}
