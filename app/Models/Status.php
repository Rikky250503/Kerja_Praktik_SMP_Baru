<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Status extends Model
{
    use HasFactory;

    protected $table = 'tbl_status';
    protected $primaryKey = 'id_status';
    protected $fillable = ['nama_status'];

    public function barang_keluar(): HasOne
    {
        return $this->hasOne(Barangkeluar::class,'id_status');
    }
}
