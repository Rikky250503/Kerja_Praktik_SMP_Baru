<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $kategori = Kategori::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Kategori Ditemukan',
                'data'  => $kategori
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
                "nama_kategori" => "required|String"
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal membuat kategori baru",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            // Hashing password
            try {
                $createdKategori = Kategori::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Suskses membuat sebuah kategori",
                    "data" => $createdKategori
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Gagal membuat sebuah kategori",
                    "error" => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_kategori)
    {
        try {
            $kategori = Kategori::findOrFail($id_kategori);

            return response()->json([
                "message" => "Berhasil ditemukan data kategori",
                "data" => $kategori
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data kategori",
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
                'nama_kategori' => 'String'
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data kategori",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $kategori = Kategori::findOrFail($id);
                DB::commit();
                if (!$kategori) {
                    return response()->json([
                        "message" => "Data kategori tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $kategori->update($Validated);
                return response()->json([
                    "message" => "Data kategori berhasil diperbarui",
                    "data" => $kategori
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data katgeori gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_kategori)
    {
        $data_kategori = Kategori::find($id_kategori);
        if(empty($data_kategori))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_kategori->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
