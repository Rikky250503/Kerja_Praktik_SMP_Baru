<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Useradmin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UseradminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $useradmin = Useradmin::all(); 
            return response()->json([
                'status' => true,
                'message'   => 'Data Useradmin Ditemukan',
                'data'  => $useradmin,
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
                "username_user" => "required|String",
                "password_user" => "required|String",
                "nama_user" => "required|String",
                "jabatan_user" => "required|String"
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                "message" => "Gagal membuat user baru",
                "error" => $validasi->errors()
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $validated = $validasi->validated();
            $validated["password_user"] = bcrypt($validated["password_user"]);
            // Hashing password
            try {
                $createdUser = Useradmin::create($validated);
                DB::commit();
                return response()->json([
                    "message" => "Suskses membuat sebuah user",
                    "data" => $createdUser
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Failed creating a new User",
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
            $useradmin = Useradmin::findOrFail($id);

            return response()->json([
                "message" => "Berhasil ditemukan data admin",
                "data" => $useradmin
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "message" => "Gagal ditemukan data admin",
                "error" => $e->getMessage()
            ]);
        }
    }
    public function login(Request $request)
        {
        try {
            $username = $request->input("username");
            $password = $request->input("password");
            $useradmin = Useradmin::where("username_user", $username)->first();
            if (!$useradmin) {
                return response()->json([
                    'message' => 'Username tidak ditemukan'
                ], Response::HTTP_NOT_FOUND);
            }
            if (password_verify($password, $useradmin->password_user)) {
                if($useradmin->status == 'aktif')
                {
                    if($useradmin->jabatan_user =='P'){
                       $token = $useradmin->createToken('Sinar Matahari Prima', ['read-useradmin','update-useradmin'])->plainTextToken;
                    }
                    else if($useradmin->jabatan_user == 'J'){
                        $token = $useradmin->createToken('Sinar Matahari Prima', ['read-barang','add-barangmasuk','read-barangmasuk','update-barangmasuk','read-detailbarangmasuk','add-detailbarangmasuk','update-detailbarangmasuk','add-supplier','read-supplier','add-customer','read-customer','read-barangkeluar','add-barangkeluar','update-barangkeluar','read-detailbarangkeluar','add-detailbarangkeluar','update-detailbarangkeluar'])->plainTextToken;
                    }
                    else if($useradmin->jabatan_user == 'G'){
                       $token = $useradmin->createToken('Sinar Matahari Prima', ['read-barang','read-barangmasuk','read-barangkeluar','read-detailbarangkeluar','update-barangkeluar'])->plainTextToken;
                   }
                   return response()->json([
                       'message' => 'Login berhasil',
                       'data' => $useradmin->id_user,
                       'jabatan' => $useradmin->jabatan_user,
                       'token' => $token,
                   ], Response::HTTP_OK);
                } 
                else if($useradmin ->status == 'nonaktif') {
                    return response()->json([
                        'message' =>'akun anda sudah tidak aktif lagi'
                    ],Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return response()->json([
                    'message' => 'Username atau Password yang Anda masukkan salah'
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        $Validator = Validator::make(
            $request->all(),
            [
                'status'=>'String'
            ]
        );
        if ($Validator->fails()) {
            return response()->json([
                "message" => "Gagal melakukan validasi tipe data admin",
                "error" => $Validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $Validated = $Validator->validated();
            
            try {
                $useradmin = Useradmin::findOrFail($id);
                DB::commit();
                if (!$useradmin) {
                    return response()->json([
                        "message" => "Data admin tidak ditemukan"
                    ], Response::HTTP_NOT_FOUND);
                }
                $useradmin->update($Validated);
                return response()->json([
                    "message" => "Data admin berhasil diperbarui",
                    "data" => $useradmin
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    "message" => "Data admin gagal diperbarui",
                    "error" => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function destroy(string $id_user)
    {
        $data_user = Useradmin::find($id_user);
        if(empty($data_user))
        {
            return response()->json([
                'status' =>false,
                'message'=>'Data tidak ditemukan'
            ],400);
        }

        $_POST = $data_user->delete();

        return response()->json([
            'status' => true,
            'message'=>'Sukses melakukan delete data'
        ]);
    }
}
