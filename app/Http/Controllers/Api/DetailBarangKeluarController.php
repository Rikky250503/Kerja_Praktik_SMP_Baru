<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Barangkeluar;
use App\Models\DetailBarangKeluar;
use App\Models\Kategori;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DetailBarangKeluarController extends Controller
{
    public function index()
    {
        try
        {
            $detailbarangkeluar = DetailBarangKeluar::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Detail barang keluar ditemukan',
                'data'  => $detailbarangkeluar
            ],Response::HTTP_OK);
        }
        catch(\Exception $e)
        {
            $e->getMessage();
        }
            return response()->json([
                'status'=> false,
                'message'=> 'Internal Server Error'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function store(Request $request)
    {
        $Validator = Validator::make(
            $request->all(),
            [
                "id_barang" => "required|String",
                "id_barang_keluar" => "required|String",
                "kuantitas"=>"required|Integer",
                "harga_satuan_keluar" =>"required|Numeric",
            ]
        );
        if ($Validator->fails()){
            return response()->json([
                "message" =>"Gagal melakukan validasi tipe data barang",
                "error" => $Validator->errors()
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        else{
            $Validated = $Validator->validate();

            DB::beginTransaction();
            $id_barang = $request->input("id_barang");
            $id_barangKeluar = $request->input('id_barang_keluar');
            $kuantitas = $request->input('kuantitas');
            $Barang = barang::findOrFail($id_barang);
            $BarangKeluar = Barangkeluar::findOrFail($id_barangKeluar);
            $harga = $request->input('harga_satuan_keluar');  
            $Totallama = $BarangKeluar->total;
            if(!$Barang){
                return response()->json([
                    'message' =>'id barang tidak di temukan',
                ],Response::HTTP_NOT_FOUND);
            }
            $total = $kuantitas * $harga;
            $Totalbaru = $Totallama +$total;
            try{
                $detailbarangkeluar = new DetailBarangKeluar();
                $detailbarangkeluar->id_barang = $id_barang;
                $detailbarangkeluar->id_barang_keluar = $id_barangKeluar;
                $detailbarangkeluar->kuantitas = $kuantitas;
                $detailbarangkeluar->harga_barang_keluar = $harga;
                $detailbarangkeluar->total = $total;
                $detailbarangkeluar->save();
                $stok_barang = [
                    $Barang->kuantitas -= $kuantitas
                ];
                $update_barang_keluar=[
                  $BarangKeluar->total = $Totalbaru 
                ];
                $Barang->update($stok_barang);
                $BarangKeluar->update($update_barang_keluar);

                DB::commit();

                return response()->json([
                    "message" => "Berhasil Menginput Data Barang",
                    "data" => $detailbarangkeluar
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data detail barang keluar  gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_detail_barang_keluar)
    {
        try {
            $detailbarangkeluar = DetailBarangKeluar::findOrFail($id_detail_barang_keluar);

            return response()->json([
                "message" => "Berhasil ditemukan data detail barang keluar",
                "data" => $detailbarangkeluar
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data detail barang keluar",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        $Validator = Validator::make(
            $request->all(),
            [
                "id_barang" => "String",
                "id_barang_keluar" => "String",
                "kuantitas" => "Integer",
                "harga_barang_keluar" => "Numeric",
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang keluar",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            $id_barang_keluar = $request->input('id_barang_keluar');
            $id_barang = $request->input('id_barang');
            $kuantitas = $request->input('kuantitas');
            $harga_barang_keluar = $request->input('harga_barang_keluar');
            $BarangKeluar = BarangKeluar::findOrFail($id_barang_keluar);
            if (!$BarangKeluar) {
                return response()->json([
                    "message" => "Data barang keluar tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $DetailBarangKeluar = DetailBarangKeluar::findOrFail($id);
            if (!$DetailBarangKeluar) {
                return response()->json([
                    "message" => "Data detail barang keluar tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $Barang = Barang::findOrFail($id_barang);
            if (!$Barang) {
                return response()->json([
                    "message" => "Data barang tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $TotalLamaDetailBarang = $DetailBarangKeluar->total;
            $TotalLamaBarangKeluar = $BarangKeluar->total;

            try {
                if ($harga_barang_keluar || $kuantitas) {
                    if (!$harga_barang_keluar) {
                        $harga_barang = $DetailBarangKeluar->harga_barang_keluar;
                    }
                    if (!$kuantitas) {
                        $totalLama = $TotalLamaBarangKeluar - $TotalLamaDetailBarang;
                        $kalkulasi_harga = ($DetailBarangKeluar->kuantitas * $harga_barang_keluar);
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_keluar = [
                            $BarangKeluar->total = $total
                        ];
                        $kuantitasbaru = $Barang->kuantitas;
                        $update_barang = [
                            $Barang->kuantitas == $kuantitas
                        ];
                    }
                    if ($kuantitas) {
                        if ($DetailBarangKeluar->kuantitas < $kuantitas) {
                            $kuantitasbaru = $kuantitas - $DetailBarangKeluar->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas -= $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangKeluar->kuantitas > $kuantitas) {
                            $kuantitasbaru = $DetailBarangKeluar->kuantitas - $kuantitas;
                            $update_barang = [
                                $Barang->kuantitas += $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangKeluar->kuantitas == $kuantitas) {
                            $kuantitasbaru = $Barang->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas == $kuantitas
                            ];
                        }
                        $totalLama = $TotalLamaBarangKeluar - $TotalLamaDetailBarang;
                        $kalkulasi_harga = $kuantitas * $harga_barang;
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_keluar = [
                            $BarangKeluar->total = $total
                        ];
                    }
                }
                DB::commit();
                
                $DetailBarangKeluar->update($Validated);
                $BarangKeluar->update($update_barang_keluar);
                $Barang->update($update_barang);
                return response()->json([
                    "message" => "Data detail barang keluar berhasil diperbarui",
                    "data" => $DetailBarangKeluar
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data detail barang keluar gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_detail_barang_keluar)
    {
        $data_detail_barang_keluar = DetailBarangKeluar::find($id_detail_barang_keluar);
        if(empty($data_detail_barang_keluar))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_detail_barang_keluar->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }

    public function updategpt(Request $request, string $id)
    {
        DB::beginTransaction();
        $Validator = Validator::make(
            $request->all(),
            [
                "id_barang" => "String",
                "id_barang_keluar" => "String",
                "kuantitas" => "Integer",
                "harga_barang_keluar" => "Numeric",
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang keluar",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            $id_barang_keluar = $request->input('id_barang_keluar');
            $id_barang = $request->input('id_barang');
            $kuantitas = $request->input('kuantitas');
            $harga_barang_keluar = $request->input('harga_barang_keluar');
            $BarangKeluar = BarangKeluar::findOrFail($id_barang_keluar);
            if (!$BarangKeluar) {
                return response()->json([
                    "message" => "Data barang keluar tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $DetailBarangKeluar = DetailBarangKeluar::findOrFail($id);
            if (!$DetailBarangKeluar) {
                return response()->json([
                    "message" => "Data detail barang keluar tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $Barang = Barang::findOrFail($id_barang);
            if (!$Barang) {
                return response()->json([
                    "message" => "Data barang tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $TotalLamaDetailBarang = $DetailBarangKeluar->total;
            $TotalLamaBarangKeluar = $BarangKeluar->total;

            try {
                if ($harga_barang_keluar || $kuantitas) {
                    if (!$harga_barang_keluar) {
                        $harga_barang = $DetailBarangKeluar->harga_barang_keluar;
                    }
                    if (!$kuantitas) {
                        $totalLama = $TotalLamaBarangKeluar - $TotalLamaDetailBarang;
                        $kalkulasi_harga = ($DetailBarangKeluar->kuantitas * $harga_barang_keluar);
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_keluar = [
                            $BarangKeluar->total = $total
                        ];
                        $kuantitasbaru = $Barang->kuantitas;
                        $update_barang = [
                            $Barang->kuantitas == $kuantitas
                        ];
                    }
                    if ($kuantitas) {
                        if ($DetailBarangKeluar->kuantitas < $kuantitas) {
                            $kuantitasbaru = $kuantitas - $DetailBarangKeluar->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas -= $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangKeluar->kuantitas > $kuantitas) {
                            $kuantitasbaru = $DetailBarangKeluar->kuantitas - $kuantitas;
                            $update_barang = [
                                $Barang->kuantitas += $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangKeluar->kuantitas == $kuantitas) {
                            $kuantitasbaru = $Barang->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas == $kuantitas
                            ];
                        }
                        $totalLama = $TotalLamaBarangKeluar - $TotalLamaDetailBarang;
                        $kalkulasi_harga = $kuantitas * $harga_barang;
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_keluar = [
                            $BarangKeluar->total = $total
                        ];
                    }
                }
                DB::commit();
                $DetailBarangKeluar->update($Validated);
                $BarangKeluar->update($update_barang_keluar);
                $Barang->update($update_barang);
                return response()->json([
                    "message" => "Data detail barang keluar berhasil diperbarui",
                    "data" => $DetailBarangKeluar
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data detail barang keluar gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function tampilDetail(String $id)
    {
        $data = DB::select
        ('SELECT tbl_customer.nama_pemesan ,tbl_customer.alamat_pemesan ,Left(tbl_barangkeluar.tanggal_keluar,10) as tanggal_keluar ,tbl_status.nama_status ,tbl_barang.nama_barang ,tbl_detail_barang_keluar.kuantitas,tbl_detail_barang_keluar.harga_barang_keluar 
        FROM tbl_detail_barang_keluar 
        JOIN tbl_barangkeluar ON tbl_detail_barang_keluar.id_barang_keluar = tbl_barangkeluar.id_barang_keluar 
        JOIN tbl_status ON tbl_status.id_status = tbl_barangkeluar.id_status 
        JOIN tbl_barang ON tbl_detail_barang_keluar.id_barang = tbl_barang.id_barang 
        JOIN tbl_customer ON tbl_customer.id_customer = tbl_barangkeluar.id_customer 
        WHERE tbl_barangkeluar.id_barang_keluar = :id',['id'=> $id]);
        try {
            if (!$data) {
                return response()->json([
                    "message" => "Data detail barang keluar tidak ditemukan"
                ], Response::HTTP_NOT_FOUND);
            }
            else{
                return response()->json([
                    "message" => "Data detail barang keluar berhasil ditemukan",
                    "data" => $data
                ], Response::HTTP_OK);
            }
        } catch (\Exception $error) {
            return response()->json([
                "message" => "Terjadi kesalahan: " . $error->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
