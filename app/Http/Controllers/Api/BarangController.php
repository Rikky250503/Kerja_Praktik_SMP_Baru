<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $barang = Barang::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Barang Ditemukan',
                'data'  => $barang
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
        DB::beginTransaction();
        
        $Validator = Validator::make(
            $request->all(),
            [
                'id_kategori' => 'required',
                'nama_barang' => 'required|String',
                'kuantitas' => 'required|Integer',
                'harga' => 'required'
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $barang = Barang::create($Validated);
                DB::commit();
                return response()->json([
                    "message" => "Data barang berhasil dimasukkan",
                    "data" => $barang
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data barang gagal dimasukkan",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_barang)
    {
        try {
            $barang = Barang::findOrFail($id_barang);

            return response()->json([
                "message" => "Berhasil ditemukan data barang",
                "data" => $barang
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data barang",
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
                "id_kategori" => "String",
                "nama_barang" => "String",
                "kuantitas" => "Integer",
                "harga" => "Numeric",
            ]   
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data barang",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $barang =  Barang::findOrFail($id);
                DB::commit();
                if (!$barang) {
                    return response()->json([
                        "message" => "Data barang tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $barang->update($Validated);
                return response()->json([
                    "message" => "Data barang berhasil diperbarui",
                    "data" => $barang
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data barang gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_barang)
    {
        $data_barang = Barang::find($id_barang);
        if(empty($data_barang))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_barang->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
