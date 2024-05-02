<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $status = Status::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Status Ditemukan',
                'data'  => $status
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
                "nama_status" => "required|String"
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal membuat Status baru",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            
            try {
                $createdStatus = Status::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Suskses membuat sebuah status",
                    "data" => $createdStatus
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Gagal membuat sebuah status",
                    "error" => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_status)
    {
        try {
            $status = Status::findOrFail($id_status);

            return response()->json([
                "message" => "Berhasil ditemukan data status",
                "data" => $status
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data status",
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
                'nama_status' => 'String'
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data status",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            try {
                $status = Status::findOrFail($id);
                DB::commit();
                if (!$status) {
                    return response()->json([
                        "message" => "Data status tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $status->update($Validated);
                return response()->json([
                    "message" => "Data status berhasil diperbarui",
                    "data" => $status
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data status gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_status)
    {
        $data_status = Status::find($id_status);
        if(empty($data_status))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_status->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
