<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $supplier = Supplier::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Supplier Ditemukan',
                'data'  => $supplier
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
        $validasi = Validator::make(
            $request->all(),
            [
                "nama_supplier" => "required|String",
                "no_hp"=> "required|String",
                "alamat"=> "required|String"
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal membuat Supplier baru",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            // Hashing password
            try {
                $createdSupplier = Supplier::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Suskses membuat sebuah supplier baru",
                    "data" => $createdSupplier
                ]);
            } catch (\Exception $e) {
                
                return response()->json([
                    "message" => "Gagal membuat sebuah supplier ",
                    "error" => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_supplier)
    {
        try {
            $supplier = Supplier::findOrFail($id_supplier);
            return response()->json([
                "message" => "Berhasil ditemukan data supplier",
                "data" => $supplier
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data supplier",
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
                'nama_supplier' => 'String',
                'no_hp'=> 'String',
                'alamat'=> 'String'
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data supplier",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $supplier = Supplier::findOrFail($id);
                DB::commit();

                if (!$supplier) {
                    return response()->json([
                        "message" => "Data Supplier tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $supplier->update($Validated);
                return response()->json([
                    "message" => "Data Supplier berhasil diperbarui",
                    "data" => $supplier
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data supplier gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_supplier)
    {
        $data_supplier = Supplier::find($id_supplier);
        if(empty($data_supplier))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_supplier->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
