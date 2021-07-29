<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Donatur;
use Validator;

class DonaturController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except('infoDonatur');
    }

    public function infoDonatur() {
        $data_donatur = Donatur::with('posko')->orderBy('nama')->get();

        $results = [];

        foreach($data_donatur as $donatur){
            $results[] = [
                'id' => $donatur->id,
                'nama' => $donatur->nama,
                'jenis_kebutuhan' => $donatur->jenis_kebutuhan,
                'keterangan' => $donatur->keterangan,
                'alamat' => $donatur->alamat,
                'id_posko' => $donatur->id_posko,
                'posko_penerima' => $donatur->posko->nama,
                'tanggal' => $donatur->tanggal,
                'jumlah' => $donatur->jumlah,
                'satuan' => $donatur->satuan,
                'created_at' => $donatur->created_at,
                'updated_at' => $donatur->updated_at,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data donatur',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function donaturByPosko($id_posko){
        $data_donatur = Donatur::where('id_posko',$id_posko)->with('posko')->orderBy('nama')->get();

        $results = [];

        foreach($data_donatur as $donatur){
            $results[] = [
                'id' => $donatur->id,
                'nama' => $donatur->nama,
                'jenis_kebutuhan' => $donatur->jenis_kebutuhan,
                'keterangan' => $donatur->keterangan,
                'alamat' => $donatur->alamat,
                'id_posko' => $donatur->id_posko,
                'posko_penerima' => $donatur->posko->nama,
                'tanggal' => $donatur->tanggal,
                'jumlah' => $donatur->jumlah,
                'satuan' => $donatur->satuan,
                'created_at' => $donatur->created_at,
                'updated_at' => $donatur->updated_at,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data donatur',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function tambahDonatur(Request $request) {
        $rules =[
            'nama' => 'required',
            'jenis_kebutuhan' => 'required',
            'keterangan' => 'required',
            'alamat' => 'required',
            'id_posko' => 'required',
            'tanggal' => 'required',
            'jumlah' => 'required',
            'satuan' => 'required',
        ];

        $messages = [
            'required' => ':attribute tidak boleh kosong'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 409,
                'error' => $validation->errors()
            ], 409);
        }

        $data_donatur = Donatur::create([
            'nama' => $request->nama,
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'alamat' => $request->alamat,
            'id_posko' => $request->id_posko,
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return response()->json([
            'message' => 'Berhasil menambahkan data donatur',
            'status' => 200,
            'data' => $data_donatur
        ],200);
    }

    public function ubahDonatur(Request $request, $id) {
        $data_donatur = Donatur::findOrFail($id);

        $data_donatur->update([
            'nama' => $request->nama,
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'alamat' => $request->alamat,
            'id_posko' => $request->id_posko,
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data donatur',
            'status' => 200,
            'data' => $data_donatur
        ],200);
    }

    public function hapusDonatur($id) {
        $data_donatur = Donatur::findOrFail($id);
        $data_donatur->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data donatur',
            'status' => 200,
            'data' => $data_donatur
        ],200);

    }
}
