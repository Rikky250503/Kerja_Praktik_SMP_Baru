<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangkeluar extends Model
{
    use HasFactory, HasUuids;

    protected $table  = 'tbl_barangkeluar';
    protected $primaryKey = 'id_barang_keluar';
    protected $fillable = ['nomor_invoice_keluar','tanggal_keluar','total','id_customer','id_status','created_by'];

    public function detailBarangKeluar() : HasMany
    {
        return $this->hasMany(DetailBarangKeluar::class,'id_barang_keluar');
    }

    public function status() :BelongsTo
    {
        return $this->belongsTo(Status::class,'id_status');
    }

    public function useradmin() : BelongsTo
    {
        return $this->belongsTo(Useradmin::class,'id_user');
    }
    
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class,'id_customer');
    }
}
