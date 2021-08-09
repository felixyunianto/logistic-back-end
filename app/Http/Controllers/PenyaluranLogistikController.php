<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PenyaluranLogistik;
use App\Logistik;

class PenyaluranLogistikController extends Controller
{
    public function __construct(){
        $this->middleware("auth:api")->except('infoPenyaluranByPosko');
    }

    public function infoPenyaluran(){
        $penyaluran = PenyaluranLogistik::where('pengirim_id', auth()->user()->id_posko)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data penyaluran',
            'status' => 200,
            'data' => $penyaluran
        ]);
    }

    public function infoPenyaluranByPosko($id_posko){
        $penyaluran = PenyaluranLogistik::where('pengirim_id', $id_posko)->get();

        $results = [];

        foreach($penyaluran as $p){
            $results[] = [
                'id' => $p->id,
                'jenis_kebutuhan' => $p->jenis_kebutuhan,
                'keterangan' => $p->keterangan,
                'jumlah' => $p->jumlah,
                'status' => $p->status,
                'pengirim_id' => $p->pengirim_id,
                'nama_pengirim' => $p->posko_pengirim->nama,
                'satuan' => $p->satuan,
                'tanggal' => $p->tanggal,
                'id_produk' => $p->id_produk,
                'nama_produk' => $p->produk->nama_produk,
                'penerima' => $p->penerima
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data penyaluran',
            'status' => 200,
            'data' => $results
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
