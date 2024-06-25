<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Useradmin extends Authenticatable
{
    use HasUuids, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_useradmin';
    protected $primaryKey = 'id_user';
    protected $fillable = ['username_user','jabatan_user','nama_user','password_user','status'];

    public function barangmasuk(): HasOne
    {
        return $this->hasOne(BarangMasuk::class,'id_user');
    }
    public function barangkeluar(): HasOne
    {
        return $this->hasOne(BarangMasuk::class,'id_user');
    }
}
