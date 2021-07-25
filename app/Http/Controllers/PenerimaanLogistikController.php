<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PenerimaanLogistik;
use App\LogistikKeluar;
use App\Logistik;

class PenerimaanLogistikController extends Controller
{
    public function __construct(){
        $this->middleware("auth:api");
    }



    public function infoPenerimaan(){
        $penerimaan = PenerimaanLogistik::orderBy('tanggal','DESC')->where('penerima_id', auth()->user()->id_posko)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data penerimaan',
            'status' => 200,
            'data' => $penerimaan
        ]);
    }

    public function tambahPenerimaan(Request $request, $id){
        $keluar = LogistikKeluar::findOrFail($id);
        $produk = Logistik::where('id', $keluar->id_produk)->first();
        $produk_penerimaan = Logistik::where('nama_produk', $produk->nama_produk)->where('id_posko', $keluar->penerima_id)->where('satuan', $keluar->satuan)->first();

        if($produk_penerimaan == null){
            $produk_penerimaan = Logistik::create([
                'nama_produk' => $produk->nama_produk,
                'satuan' => $produk->satuan,
                'jumlah' => $keluar->jumlah,
                'id_posko' => $keluar->penerima_id
            ]);
        }else{
            $produk_penerimaan->update([
                'jumlah' => (int) $produk_penerimaan->jumlah + (int) $keluar->jumlah
            ]);
        }

        PenerimaanLogistik::create([
            'jenis_kebutuhan' => $keluar->jenis_kebutuhan,
            'keterangan' => 'Barang masuk '. $produk_penerimaan->nama_produk .' tanggal '. $keluar->tanggal,
            'jumlah' => $keluar->jumlah,
            'status' => 'Terima',
            'pengirim_id' => $keluar->pengirim_id,
            'satuan' => $keluar->satuan,
            'tanggal' => $keluar->tanggal,
            'id_produk' => $produk_penerimaan->id,
            'penerima_id' => $keluar->penerima_id
        ]);

        $keluar->update(['status' => 'Terima']);

        return response()->json([
            'data' => 'Berhasil menerima',
            'status' => 200,
            'data' => 'BERHASIL'
        ], 200);
        
    }
}
