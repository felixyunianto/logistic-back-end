<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KebutuhanLogistik;

class KebutuhanLogistikController extends Controller
{
    // public function __construct(){
    //     $this->middleware('auth:api');
    // }
    public function infoKebutuhanLogistik(){
        $data_kebutuhan_logistik = KebutuhanLogistik::all();

        // $results = [];

        // foreach($data_kebutuhan_logistik as $kebutuhan_logistik){
        //     $results[] = [
        //         "id" => $kebutuhan_logistik->id,
        //         "id_posko" => $kebutuhan_logistik->id_posko,
        //         "posko" => $kebutuhan_logistik->posko->nama,
        //         "jenis_kebutuhan" => $kebutuhan_logistik->jenis_kebutuhan,
        //         "keterangan" => $kebutuhan_logistik->keterangan,
        //         "jumlah" => $kebutuhan_logistik->jumlah,
        //         "status" => $kebutuhan_logistik->status,
        //         "satuan" => $kebutuhan_logistik->satuan,
        //         "tanggal" => $kebutuhan_logistik->tanggal,
        //         "created_at" => $kebutuhan_logistik->created_at,
        //         "updated_at" => $kebutuhan_logistik->updated_at,
        //     ];
        // }



        return response()->json([
            'message' => 'Berhasil menampilkan kebutuhan logistik',
            'status' => 200,
            'data' => $data_kebutuhan_logistik
        ],200);
    }

    public function infoKebutuhanLogistikByPosko($id){
        $data_kebutuhan_logistik = KebutuhanLogistik::where('id_posko',$id)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan kebutuhan logistik',
            'status' => 200,
            'data' => $data_kebutuhan_logistik
        ],200);
    }

    public function tambahKebutuhanLogistik(Request $request){
        $rules = [
            'id_posko' => 'required',
            'jenis_kebutuhan' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required',
            'status' => 'required|in:Terpenuhi,Belum Terpenuhi',
            'tanggal' => 'required'
        ];

        $messages = [
            'required' => 'Bidang :attribute tidak boleh kosong',
            'in' => 'Pemilihan :attribute salah atau tidak ditemukan'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()){
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 403,
                'error' => $validation->errors()
            ]);
        }

        $data_kebutuhan_logistik = KebutuhanLogistik::create([
            'id_produk' => $request->id_produk,
            'id_posko' => $request->id_posko,
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal
        ]);

        return response()->json([
            'message' => 'Berhasil menambahkan data kebutuhan logistik',
            'status' => 200,
            'data' => $data_kebutuhan_logistik
        ],200);
    }

    public function ubahKebutuhanLogistik(Request $request, $id){
        $data_kebutuhan_logistik = KebutuhanLogistik::findOrFail($id);
        $data_kebutuhan_logistik->update([
            'id_produk' => $request->id_produk,
            'id_posko' => $request->id_posko,
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data kebutuhan logistik',
            'status' => 200,
            'data' => $data_kebutuhan_logistik
        ]);
    }

    public function hapusKebutuhanLogistik($id) {
        $data_kebutuhan_logistik = KebutuhanLogistik::findOrFail($id);
        $data_kebutuhan_logistik->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data kebutuhan logistik',
            'status' => 200,
            'data' => $data_kebutuhan_logistik
        ]);        
    }
}
