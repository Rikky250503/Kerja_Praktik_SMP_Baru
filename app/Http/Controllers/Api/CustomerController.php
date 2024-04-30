<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $barangkeluar = Customer::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Customer Ditemukan',
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        // untuk validasi sesuai type data
        $validasi = Validator::make(
            $request->all(),
            [
                "nama_pemesan" => "required|String",
                "alamat_pemesan" => "required|String",
                "no_hp_pemesan" => "required|String",
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal menginput data customer",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            try {
                $bikincustomer = Customer::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Berhasil menginput data customer",
                    "data" => $bikincustomer
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Gagal menginput data customer",
                    "error" => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return response()->json([
                "message" => "Berhasil ditemukan data barang keluar",
                "data" => $customer
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
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        $Validator = Validator::make(
            $request->all(),
            [
                "nama_pemesan" => "String",
                "alamat_pemesanan" => "String",
                "no_hp_pemesan" => "String",
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data customer",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $customer =  Customer::findOrFail($id);
                DB::commit();
                if (!$customer) {
                    return response()->json([
                        "message" => "Data customer tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $customer->update($Validated);
                return response()->json([
                    "message" => "Data customer berhasil diperbarui",
                    "data" => $customer
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data customer gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data_customer = Customer::find($id);
        if(empty($data_customer))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_customer->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
