<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory,HasUuids;

    protected $table = "tbl_customer";
    protected $primaryKey = 'id_customer';
    protected $fillable = ['nama_pemesan','alamat_pemesan','no_hp_pemesan',];


    public function barangkeluar(): HasMany
    {
        return $this->hasMany(Barang::class,'id_customer');
    }
}
