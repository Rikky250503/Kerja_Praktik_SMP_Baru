<?php

use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\BarangMasukController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DetailBarangKeluarController;
use App\Http\Controllers\Api\DetailBarangMasukController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UseradminController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

 Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
     return $request->user();
    
 });

Route::post('useradmin/daftar', [UseradminController::class,'store']);
Route::post('useradmin/login', [UseradminController::class,'login']);
//ini yang ada abilities

Route::middleware('auth:sanctum')->group(function () {
    //DONE
    Route::prefix('useradmin')->group(function(){
        Route::get('/', [UseradminController::class,'index']);
        Route::get('/detail/{id}', [UseradminController::class,'show']);
        Route::put('/update/{id}', [UseradminController::class,'update']);
        Route::delete('/delete/{id_user}', [UseradminController::class,'destroy']);
    });
    //DONE
    Route::prefix('status')->group(function(){
        Route::get('/', [StatusController::class,'index']);
        Route::post('/daftar', [StatusController::class,'store']);
        Route::get('/detail/{id_status}', [StatusController::class,'show']);
        Route::put('/update/{id}', [StatusController::class,'update']);
        Route::delete('/delete/{id_status}', [StatusController::class,'destroy']);
    });
    //DONE
    Route::prefix('kategori')->group(function(){
        Route::get('/', [KategoriController::class,'index']);
        Route::post('/daftar', [KategoriController::class,'store']);
        Route::get('/detail/{id_kategori}', [KategoriController::class,'show']);
        Route::put('/update/{id}', [KategoriController::class,'update']);
        Route::delete('/delete/{id_kategori}', [KategoriController::class,'destroy']);
    });
    
    Route::prefix('supplier')->group(function(){
        Route::middleware(['auth:sanctum','abilities:read-supplier'])->get('/', [SupplierController::class,'index']);
        Route::middleware(['auth:sanctum', 'abilities:add-supplier'])->post('/daftar', [SupplierController::class, 'store']);
        Route::middleware(['auth:sanctum','abilities:read-supplier'])->get('/detail/{id_supplier}', [SupplierController::class,'show']);
        Route::put('/update/{id}', [SupplierController::class,'update']);
        Route::delete('/delete/{id_supplier}', [SupplierController::class,'destroy']);
    });
    
    Route::prefix('barang')->group(function(){
        Route::middleware(['auth:sanctum', 'abilities:read-barang'])->get('/', [BarangController::class, 'index']);
        Route::post('/daftar', [BarangController::class,'store']);
        Route::middleware(['auth:sanctum', 'abilities:read-barang'])->get('/detail/{id_barang}', [BarangController::class, 'show']);
        Route::put('/update/{id}', [BarangController::class,'update']);
        Route::delete('/delete/{id_barang}', [BarangController::class,'destroy']);
    });
    //DONE
    Route::prefix('barangmasuk')->group(function(){
        Route::middleware(['auth:sanctum', 'abilities:read-barangmasuk'])->get('/', [BarangmasukController::class, 'index']);
        Route::middleware(['auth:sanctum', 'abilities:add-barangmasuk'])->post('/daftar', [BarangmasukController::class, 'store']);
        Route::middleware(['auth:sanctum','abilities:read-barangmasuk'])->get('/list', [BarangmasukController::class,'tampilList']);
        Route::middleware(['auth:sanctum', 'abilities:read-barangmasuk'])->get('/detail/{id_barang_masuk}', [BarangmasukController::class, 'show']);
        Route::middleware(['auth:sanctum', 'abilities:update-barangmasuk'])->put('/update/{id}', [BarangmasukController::class, 'update']);
        Route::delete('/delete/{id_barang_masuk}', [BarangmasukController::class,'destroy']);
    });
    //DONE
    Route::prefix('detailbarangmasuk')->group(function(){
        Route::middleware(['auth:sanctum', 'abilities:read-detailbarangmasuk'])->get('/', [DetailBarangmasukController::class, 'index']);
        Route::middleware(['auth:sanctum', 'abilities:add-detailbarangmasuk'])->post('/daftar', [DetailBarangmasukController::class, 'store']);
        Route::middleware(['auth:sanctum','abilities:read-detailbarangmasuk'])->post('/detail/{id}', [DetailBarangmasukController::class,'tampilDetail']);
        Route::middleware(['auth:sanctum', 'abilities:read-detailbarangmasuk'])->get('/detail/{id_detail_barang_masuk}', [DetailBarangmasukController::class, 'show']);
        Route::middleware(['auth:sanctum', 'abilities:update-detailbarangmasuk'])->put('/update/{id}', [DetailBarangmasukController::class, 'update']);
        Route::delete('/delete/{id_detail_barang_masuk}', [DetailBarangmasukController::class,'destroy']);
    });
    //DONE
    Route::prefix('barangkeluar')->group(function(){
        Route::middleware(['auth:sanctum','abilities:read-barangkeluar'])->get('/',[BarangkeluarController::class,'index']);
        Route::middleware(['auth:sanctum','abilities:add-barangkeluar'])->post('/daftar',[BarangkeluarController::class,'store']);
        Route::middleware(['abilities:read-barangkeluar'])->get('/list/{tanggal?}', [BarangkeluarController::class,'tampilList']);
        Route::middleware(['auth:sanctum','abilities:read-barangkeluar'])->get('/detail/{id_barang_keluar}',[BarangkeluarController::class,'show']);
        Route::middleware(['auth:sanctum', 'abilities:update-barangkeluar'])->put('/update/{id}', [BarangkeluarController::class,'update']);
        Route::delete('/delete/{id_barang_keluar}', [BarangkeluarController::class,'destroy']);
    });
    Route::prefix('detailbarangkeluar')->group(function(){
        Route::middleware(['auth:sanctum','abilities:read-detailbarangkeluar'])->get('/', [DetailBarangkeluarController::class,'index']);
        Route::middleware(['auth:sanctum','abilities:add-detailbarangkeluar'])->post('/daftar', [DetailBarangkeluarController::class,'store']);
        Route::middleware(['auth:sanctum','abilities:read-detailbarangkeluar'])->post('/detail/{id}', [DetailBarangkeluarController::class,'tampilDetail']);
        Route::middleware(['auth:sanctum','abilities:read-detailbarangkeluar'])->get('/detail/{id_detail_barang_keluar}', [DetailBarangkeluarController::class,'show']);
        Route::middleware(['auth:sanctum', 'abilities:update-detailbarangkeluar'])->put('/update/{id}',[DetailBarangkeluarController::class,'update']);
        Route::delete('/delete/{id_detail_barang_keluar}', [DetailBarangkeluarController::class,'destroy']);
    });
    Route::prefix('customer')->group(function(){
        Route::middleware(['auth:sanctum','abilities:read-customer'])->get('/', [CustomerController::class,'index']);
        Route::middleware(['auth:sanctum','abilities:add-customer'])->post('/daftar', [CustomerController::class,'store']);
        Route::middleware(['auth:sanctum','abilities:read-customer'])->get('/detail/{id}', [CustomerController::class,'show']);
        Route::put('/update/{id}',[CustomerController::class,'update']);
        Route::delete('/delete/{id}', [CustomerController::class,'destroy']);
    });
});


//ini yang buat tester

//Route::middleware('auth:sanctum')->group(function () {
    //DONE
/*
    Route::get('/status1', function () {
        return 'test' ;
    });

Route::prefix('useradmin')->group(function(){
    Route::get('/', [UseradminController::class,'index']);
    Route::get('/detail/{id}', [UseradminController::class,'show']);
    Route::put('/update/{id}', [UseradminController::class,'update']);
    Route::delete('/delete/{id_user}', [UseradminController::class,'destroy']);
});
//DONE
Route::prefix('status')->group(function(){
    Route::get('/', [StatusController::class,'index']);
    Route::post('/daftar', [StatusController::class,'store']);
    Route::get('/detail/{id_status}', [StatusController::class,'show']);
    Route::put('/update/{id}', [StatusController::class,'update']);
    Route::delete('/delete/{id_status}', [StatusController::class,'destroy']);
});
//DONE
Route::prefix('kategori')->group(function(){
    Route::get('/', [KategoriController::class,'index']);
    Route::post('/daftar', [KategoriController::class,'store']);
    Route::get('/detail/{id_kategori}', [KategoriController::class,'show']);
    Route::put('/update/{id}', [KategoriController::class,'update']);
    Route::delete('/delete/{id_kategori}', [KategoriController::class,'destroy']);
});

Route::prefix('supplier')->group(function(){
    Route::get('/', [SupplierController::class,'index']);
    Route::post('/daftar', [SupplierController::class,'store']);
    Route::get('/detail/{id_supplier}', [SupplierController::class,'show']);
    Route::put('/update/{id}', [SupplierController::class,'update']);
    Route::delete('/delete/{id_supplier}', [SupplierController::class,'destroy']);
});

Route::prefix('barang')->group(function(){
    Route::get('/', [BarangController::class,'index']);
    Route::post('/daftar', [BarangController::class,'store']);
    Route::get('/detail/{id_barang}', [BarangController::class,'show']);
    Route::put('/update/{id}', [BarangController::class,'update']);
    Route::delete('/delete/{id_barang}', [BarangController::class,'destroy']);
});

    //Done
    Route::prefix('barangmasuk')->group(function(){
        Route::get('/', [BarangmasukController::class,'index']);
        Route::post('/daftar', [BarangmasukController::class,'store']);
        Route::get('/detail/{id_barang_masuk}', [BarangmasukController::class,'show']);
        Route::get('/list', [BarangmasukController::class,'tampilList']);
        Route::put('/update/{id}', [BarangmasukController::class,'update']);
        Route::delete('/delete/{id_barang_masuk}', [BarangmasukController::class,'destroy']);
    });
    //Done
Route::prefix('detailbarangmasuk')->group(function(){
    Route::get('/', [DetailBarangmasukController::class,'index']);
    Route::post('/daftar', [DetailBarangmasukController::class,'store']);
    Route::get('/detail/{id_detail_barang_masuk}', [DetailBarangmasukController::class,'show']);
    Route::put('/update/{id}', [DetailBarangmasukController::class,'update']);
    Route::delete('/delete/{id_detail_barang_masuk}', [DetailBarangmasukController::class,'destroy']);
    Route::post('/detail/{id}', [DetailBarangmasukController::class,'tampilDetail']);
});
//Done
Route::prefix('barangkeluar')->group(function(){
    Route::get('/', [BarangkeluarController::class,'index']);
    Route::post('/daftar', [BarangkeluarController::class,'store']);
    Route::get('/detail/{id_barang_keluar}', [BarangkeluarController::class,'show']);
    Route::get('/list/{tanggal?}', [BarangkeluarController::class,'tampilList']);
    Route::put('/update/{id}', [BarangkeluarController::class,'update']);
    Route::delete('/delete/{id_barang_keluar}', [BarangkeluarController::class,'destroy']);
});

Route::prefix('detailbarangkeluar')->group(function(){
    Route::get('/', [DetailBarangkeluarController::class,'index']);
    Route::post('/daftar', [DetailBarangkeluarController::class,'store']);
    Route::get('/detail/{id_detail_barang_keluar}', [DetailBarangkeluarController::class,'show']);
    Route::put('/update/{id}',[DetailBarangkeluarController::class,'update']);
    Route::delete('/delete/{id_detail_barang_keluar}', [DetailBarangkeluarController::class,'destroy']);
    Route::post('/detail/{id}', [DetailBarangkeluarController::class,'tampilDetail']);
});

Route::prefix('customer')->group(function(){
    Route::get('/', [CustomerController::class,'index']);
    Route::post('/daftar', [CustomerController::class,'store']);
    Route::get('/detail/{id}', [CustomerController::class,'show']);
    Route::put('/update/{id}',[CustomerController::class,'update']);
    Route::delete('/delete/{id}', [CustomerController::class,'destroy']);
});

//});
*/