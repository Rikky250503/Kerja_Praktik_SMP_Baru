<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangmasuk extends Model
{
    use HasFactory,HasUuids;

    protected $table ='tbl_barangmasuk';
    protected $primaryKey ='id_barang_masuk';
    protected $fillable = ['nomor_invoice_masuk','total','tanggal_masuk','id_supplier','created_by'];


    public function detailbarangmasuk(): HasMany
    {
        return $this->hasMany(DetailBarangMasuk::class,'id_barang_masuk');
    }

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class,'id_supplier');
    }
    public function useradmin(): BelongsTo
    {
        return $this->belongsTo(Useradmin::class,'id_user');
    }
}
