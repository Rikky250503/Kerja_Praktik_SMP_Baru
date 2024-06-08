<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Barangmasuk;
use App\Models\DetailBarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DetailBarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $detailbarangmasuk = DetailBarangMasuk::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Detail barang masuk ditemukan',
                'data'  => $detailbarangmasuk
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Validator = Validator::make(
            $request->all(),
            [
                'id_barang' => 'required|String',
                'id_barang_masuk' => 'required|String',
                'kuantitas' => 'required|Integer',
                'harga_satuan' => 'required|Numeric',
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();

            DB::beginTransaction();
            $id_barang = $request->input('id_barang');
            $id_barangMasuk = $request->input('id_barang_masuk');
            $kuantitas = $request->input('kuantitas');
            $harga = $request->input('harga_satuan');
            $Barang = barang::findOrFail($id_barang);
            $BarangMasuk = Barangmasuk::findOrFail($id_barangMasuk);
            $Totallama = $BarangMasuk->total;
            if(!$Barang){
                return response()->json([
                    'message' =>'id barang tidak di temukan',
                ],Response::HTTP_NOT_FOUND);
            }
            $total = $kuantitas * $harga;
            $Totalbaru = $Totallama + $total;
            try {
                $detailbarangmasuk = new DetailBarangMasuk();
                $detailbarangmasuk->id_barang = $id_barang;
                $detailbarangmasuk->id_barang_masuk = $id_barangMasuk;
                $detailbarangmasuk->kuantitas = $kuantitas;
                $detailbarangmasuk->harga_satuan = $harga;
                $detailbarangmasuk->total = $total;
                $detailbarangmasuk->save();
                $stok_barang = [
                    $Barang->kuantitas += $kuantitas
                ];
                $update_barang_masuk=[
                  $BarangMasuk->total = $Totalbaru
                ];
                $Barang->update($stok_barang);
                $BarangMasuk->update($update_barang_masuk);
            
                DB::commit();

                return response()->json([
                    "message" => "Data detail barang masuk berhasil diperbarui",
                    "data" => $detailbarangmasuk
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data detail barang masuk  gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_detail_barang_masuk)
    {
        try {
            $detailbarangmasuk = DetailBarangMasuk::findOrFail($id_detail_barang_masuk);

            return response()->json([
                "message" => "Berhasil ditemukan data detail barang masuk",
                "data" => $detailbarangmasuk
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data detail barang masuk",
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
                "id_barang_masuk"=> "String",
                "kuantitas" => "Integer",
                "harga_satuan" => "Numeric"
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang masuk",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            $id_barang_masuk = $request->input('id_barang_masuk');
            $id_barang = $request->input('id_barang');
            $kuantitas = $request->input('kuantitas');
            $harga_barang_masuk = $request->input('harga_satuan');
            $BarangMasuk = Barangmasuk::findOrFail($id_barang_masuk);
            if (!$BarangMasuk) {
                return response()->json([
                    "message" => "Data barang masuk tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $DetailBarangMasuk =  DetailBarangMasuk::findOrFail($id);
            if (!$DetailBarangMasuk) {
                return response()->json([
                    "message" => "Data detail barang masuk tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $Barang = Barang::findOrFail($id_barang);
            if (!$Barang) {
                return response()->json([
                    "message" => "Data barang tidak ditemukan",
                ], Response::HTTP_NOT_FOUND);
            }
            $TotalLamaDetailBarang = $DetailBarangMasuk->total;
            $TotalLamaBarangMasuk = $BarangMasuk->total;

            try {
                if($harga_barang_masuk || $kuantitas){
                    if (!$harga_barang_masuk) {
                        $harga_barang = $DetailBarangMasuk->harga_satuan;
                    }
                    if (!$kuantitas) {
                        $totalLama = $TotalLamaBarangMasuk - $TotalLamaDetailBarang;
                        $kalkulasi_harga = ($DetailBarangMasuk->kuantitas * $harga_barang_masuk);
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_masuk = [
                            $BarangMasuk->total = $total
                        ];
                        $kuantitasbaru = $Barang->kuantitas;
                        $update_barang = [
                            $Barang->kuantitas == $kuantitas
                        ];
                    }
                    if ($kuantitas) {
                        if ($DetailBarangMasuk->kuantitas < $kuantitas) {
                            $kuantitasbaru = $kuantitas - $DetailBarangMasuk->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas += $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangMasuk->kuantitas > $kuantitas) {
                            $kuantitasbaru = $DetailBarangMasuk->kuantitas - $kuantitas;
                            $update_barang = [
                                $Barang->kuantitas -= $kuantitasbaru
                            ];
                        }
                        if ($DetailBarangMasuk->kuantitas == $kuantitas) {
                            $kuantitasbaru = $Barang->kuantitas;
                            $update_barang = [
                                $Barang->kuantitas == $kuantitas
                            ];
                        }
                        $totalLama = $TotalLamaBarangMasuk - $TotalLamaDetailBarang;
                        $kalkulasi_harga = $kuantitas * $harga_barang;
                        $total = $kalkulasi_harga + $totalLama;
                        $Validated['total'] = $kalkulasi_harga;
                        $update_barang_masuk = [
                            $BarangMasuk->total = $total
                        ];
                    }
                }
                DB::commit();

                $DetailBarangMasuk->update($Validated);
                $BarangMasuk->update($update_barang_masuk);
                $Barang->update($update_barang);
                return response()->json([
                    "message" => "Data detail barang masuk berhasil diperbarui",
                    "data" => $DetailBarangMasuk
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data detail barang masuk gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_detail_barang_masuk)
    {
        $data_detail_barang_masuk = DetailBarangMasuk::find($id_detail_barang_masuk);
        if(empty($data_detail_barang_masuk))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_detail_barang_masuk->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }

    public function tampilDetail(String $id)
    {
        $data['detailbarang'] = DB::select('SELECT tbl_supplier.nama_supplier ,tbl_supplier.alamat,tbl_barangmasuk.tanggal_masuk ,tbl_barang.nama_barang ,tbl_detail_barang_masuk.kuantitas,tbl_detail_barang_masuk.harga_satuan FROM tbl_detail_barang_masuk JOIN tbl_barangmasuk ON tbl_detail_barang_masuk.id_barang_masuk = tbl_barangmasuk.id_barang_masuk JOIN tbl_barang ON tbl_detail_barang_masuk.id_barang = tbl_barang.id_barang JOIN tbl_supplier ON tbl_supplier.id_supplier = tbl_barangmasuk.id_supplier WHERE tbl_barangmasuk.id_barang_masuk = :id',['id'=> $id]);
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
