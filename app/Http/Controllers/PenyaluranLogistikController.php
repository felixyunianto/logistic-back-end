<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PenyaluranLogistik;
use App\Logistik;

class PenyaluranLogistikController extends Controller
{
    public function __construct(){
        $this->middleware("auth:api");
    }

    public function infoPenyaluran(){
        $penyaluran = PenyaluranLogistik::where('pengirim_id', auth()->user()->id_posko)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data penyaluran',
            'status' => 200,
            'data' => $penyaluran
        ]);
    }

    public function tambahPenyaluran(Request $request){
        $penyaluran = PenyaluranLogistik::create([
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'pengirim_id' => auth()->user()->id_posko,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal,
            'id_produk' => $request->id_produk,
            'penerima' => $request->penerima,
        ]);

        $produk = Logistik::findOrFail($request->id_produk);
        $produk->update([
            'jumlah' => (int)$produk->jumlah - (int) $request->jumlah
        ]);

        return response()->json([
            'message' => 'Berhasil menambhakan data penyaluran',
            'status' => 200,
            'data' => $penyaluran
        ]);
    }
}
