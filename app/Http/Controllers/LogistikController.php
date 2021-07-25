<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Logistik;
use Auth;
use Validator;

class LogistikController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }

    public function infoLogistik(){
        $logistik = Logistik::where('id_posko', auth()->user()->id_posko)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data logistik',
            'status' => 200,
            'data' => $logistik
        ],200);
    }

    public function tambahLogistik(Request $request){
        $rules = [
            'nama_produk' => 'required',
            'jumlah' => 'required',
            'satuan' => 'required',
        ];

        $messages = [
            'required' => ':attribute harus diisi'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 409,
                'error' => $validation->errors()
            ], 409);
        }

        $logistik = Logistik::create([
            'nama_produk' => $request->nama_produk,
            'id_posko' => auth()->user()->id_posko,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return response()->json([
            'message' => 'Berhasil menambahkan data logistik',
            'status' => 200,
            'data' => $logistik
        ],200);
    }

    public function ubahLogistik(Request $request, $id){
        $logistik = Logistik::findOrFail($id);
        $logistik->update([
            'nama_produk' => $request->nama_produk,
            'id_posko' => auth()->user()->id_posko,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data logistik',
            'status' => 200,
            'data' => $logistik
        ],200);
    }

    public function hapusLogistik($id){
        $logistik = Logistik::findOrFail($id);
        $logistik->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data logistik',
            'status' => 200,
            'data' => $logistik
        ],200);
    }
}
