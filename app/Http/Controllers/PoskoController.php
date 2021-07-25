<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Posko;
use Validator;

class PoskoController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['infoPosko']);
    }

    public function infoPosko() {
        $data_posko = Posko::orderBy('nama')->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data posko',
            'status' => 200,
            'data' => $data_posko
        ],200);
    }

    public function tambahPosko(Request $request){
        $rules = [
            'nama' => 'required',
            'jumlah_pengungsi' =>'required',
            'kontak_hp' => 'required',
            'lokasi' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ];

        $messages = [
            'required' => ':attribute tidak boleh kosong'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()){
            return response()->json([
                'message' => 'Terjadi kesahalan',
                'status' => 409,
                'error' => $validation->errors()
            ], 409);
        }

        $data_posko = Posko::create([
            'nama' => $request->nama,
            'jumlah_pengungsi' => $request->jumlah_pengungsi,
            'kontak_hp' => $request->kontak_hp,
            'lokasi' => $request->lokasi,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);


        return response()->json([
            'message' => 'Berhasil menambahkan data posko',
            'status' => 200,
            'data' => $data_posko
        ], 200);
    }

    public function ubahPosko(Request $request, $id){
        $data_posko = Posko::findOrFail($id);
        $data_posko->update([
            'nama' => $request->nama,
            'jumlah_pengungsi' => $request->jumlah_pengungsi,
            'kontak_hp' => $request->kontak_hp,
            'lokasi' => $request->lokasi,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data posko',
            'status' => 200,
            'data' => $data_posko
        ],200);
    }

    public function hapusPosko($id) {
        $data_posko = Posko::findOrFail($id);
        $data_posko->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data posko',
            'status' => 200,
            'data' => $data_posko
        ], 200);
    }
}
