<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barangkeluar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $barangkeluar = Barangkeluar::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Barang Keluar Ditemukan',
                'data'  => $barangkeluar
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
     //lom fixs
    public function store(Request $request)
    {
        DB::beginTransaction();
        // untuk validasi sesuai type data
        $validasi = Validator::make(
            $request->all(),
            [
                "nomor_invoice_keluar" => "required|String",
                "id_customer" => "required|String",
                "created_by" =>"",
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal menginput data barang keluar",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            try {
                $bikinbarangkeluar = Barangkeluar::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Berhasil menginput data barang keluar",
                    "data" => $bikinbarangkeluar
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Gagal menginput data barang keluar",
                    "error" => $e->getMessage()
                ]);
            }
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id_barang_keluar)
    {
        try {
            $barangkeluar = Barangkeluar::findOrFail($id_barang_keluar);

            return response()->json([
                "message" => "Berhasil ditemukan data barang keluar",
                "data" => $barangkeluar
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data barang keluar",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
                //lom fixs
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $Validator = Validator::make(
            $request->all(),
            [
                "tanggal_keluar" => "Date",
                "nomor_invoice_keluar" => "String",
                "total" => "Double",
                "id_status"=> "String"
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang keluar",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $barangkeluar =  Barangkeluar::findOrFail($id);
                DB::commit();
                if (!$barangkeluar) {
                    return response()->json([
                        "message" => "Data barang keluar tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $barangkeluar->update($Validated);
                return response()->json([
                    "message" => "Data barang keluar berhasil diperbarui",
                    "data" => $barangkeluar
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data barang keluar gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_barang_keluar)
    {
        $data_barangkeluar = Barangkeluar::find($id_barang_keluar);
        if(empty($data_barangkeluar))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }
        $_POST = $data_barangkeluar->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
    //api group by by tanggal

    public function tampilList($tanggal = null)
    {
        if($tanggal == null)
        {
            $data['barangkeluar']= DB::select('SELECT tbl_barangkeluar.id_barang_keluar,tbl_barangkeluar.nomor_invoice_keluar, tbl_customer.nama_pemesan, left(tbl_barangkeluar.tanggal_keluar,10) as tanggal_keluar, tbl_status.nama_status FROM tbl_barangkeluar JOIN tbl_status ON tbl_barangkeluar.id_status = tbl_status.id_status JOIN tbl_customer ON tbl_barangkeluar.id_customer = tbl_customer.id_customer ORDER BY tbl_barangkeluar.created_at DESC');
        }
        else{
            $data['barangkeluar']= DB::select('SELECT tbl_barangkeluar.id_barang_keluar,tbl_barangkeluar.nomor_invoice_keluar, tbl_customer.nama_pemesan, left(tbl_barangkeluar.tanggal_keluar,10) as tanggal_keluar, tbl_status.nama_status FROM tbl_barangkeluar JOIN tbl_status ON tbl_barangkeluar.id_status = tbl_status.id_status JOIN tbl_customer ON tbl_barangkeluar.id_customer = tbl_customer.id_customer WHERE left(tbl_barangkeluar.tanggal_keluar,10) = $tanggal ORDER BY tbl_barangkeluar.created_at DESC');
        }
        try {
            if (!$data['barangkeluar']) {
                return response()->json([
                    "message" => "Data list barang keluar tidak ditemukan"
                ], Response::HTTP_NOT_FOUND);
            } else {
                // // Panggil controller kedua dan kirimkan data barang keluar
                // $detailController = new DetailBarangKeluarController();
                // $detailController->store($data['barangkeluar']);
                return response()->json([
                    "message" => "Data barang keluar berhasil diperbarui",
                    "data" => $data['barangkeluar']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Data barang keluar gagal diperbarui",
                "error" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
