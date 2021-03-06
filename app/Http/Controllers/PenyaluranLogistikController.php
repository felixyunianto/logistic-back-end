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
        $produk = Logistik::findOrFail($request->id_produk);

        if((int)$produk->jumlah - (int)$request->jumlah < 0){
            return response()->json([
                'message' => 'Jumlah barang tidak cukup',
                'status' => 409,
            ], 409);
        }
        
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
        $produk->update([
            'jumlah' => (int)$produk->jumlah - (int) $request->jumlah
        ]);

        return response()->json([
            'message' => 'Berhasil menambhakan data penyaluran',
            'status' => 200,
            'data' => $penyaluran
        ]);
    }

    public function ubahPenyaluran(Request $request, $id){
        $produk = Logistik::findOrFail($request->id_produk);
        $penyaluran = PenyaluranLogistik::findOrFail($id);
        $jumlah_produk = $penyaluran->jumlah;
        $penyaluran->update([
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

        $produk->update([
            'jumlah' => (int)$jumlah_produk >= (int)$request->jumlah 
            ? (int)$produk->jumlah + ((int) $jumlah_produk - (int)$request->jumlah) 
            : (int)$produk->jumlah - ((int)$request->jumlah - (int) $jumlah_produk)
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data penyaluran logistik',
            'status' => 200,
            'data' => $penyaluran
        ],200);
    }

    public function hapusPenyaluran($id){
        $penyaluran = PenyaluranLogistik::findOrFail($id);
        $penyaluran->delete();

        return response()->json([
            'message' => 'Berhasil mengubah data penyaluran logistik',
            'status' => 200,
            'data' => $penyaluran
        ],200);
    }
}
