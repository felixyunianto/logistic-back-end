<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Auth;

class PetugasController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }

    public function infoPetugas(){
        $petugas = User::where('level', 'Petugas')->get();

        $results = [];

        foreach($petugas as $p){
            $results[] = [
                'id' => $p->id,
                'username' => $p->username,
                'level' => $p->level,
                'id_posko' => $p->id_posko,
                'nama_posko' => $p->posko->nama,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data petugas',
            'status' => 200,
            'data' => $results
        ], 200);
    }

    public function tambahPetugas(Request $request){
        $rules = [
            'id_posko' => 'required',
            'username' => 'required',
            'password' => 'required',
            'password_confrim' => 'required',
            'level' => 'required',
        ];

        $messages = [
            'required' => ':attribute harus diisi'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 409,
                'error' => $validation->errors()
            ], 409);
        }

        $username = User::where('username', $request->username)->first();
        

        if($username !== null){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 422,
                'error' => 'Username telah digunakan oleh user lain'
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'level' => $request->level,
            'id_posko' => $request->id_posko,
        ]);

        $respon = [
            'nama' => $user['nama'],
            'username' => $user['username'],
            'level' => $user['level'],
            'token' => $user->createToken('LogistikBrebes')->accessToken,
        ];

        return response()->json([
            'message' => 'Berhasil menambahkan petugas posko',
            'status' => 200,
            'data' => $user
        ], 200);


        
    }

    public function ubahPetugas(Request $request, $id){
        if(Auth::user()->level !== 'Admin'){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 403,
                'error' => 'Akses ini hanya dimiliki oleh admin'
            ],403);
        }

        $petugas = User::findOrFail($id);
        $petugas->update([
            'username' => $request->username,
            'password' => $request->password ? bcrypt($request->password) : $petugas->password,
            'level' => $request->level,
            'id_posko' => $request->id_posko,
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data petugas posko',
            'status' => 200,
            'data' => $petugas
        ], 200);
    }

    public function hapusPetugas($id){
        if(Auth::user()->level !== 'Admin'){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 403,
                'error' => 'Akses ini hanya dimiliki oleh admin'
            ],403);
        }
        
        $petugas = User::findOrFail($id);
        $petugas->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data petugas posko',
            'status' => 200,
            'data' => $petugas
        ], 200);
    }
}
