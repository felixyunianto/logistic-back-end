<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LogistikMasuk;
use App\Logistik;
use Cloudinary;
use Validator;
use Carbon\Carbon;

class LogistikMasukController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except("infoLogistikMasuk");
    }

    public function infoLogistikMasuk(){
        $logistik_masuk = LogistikMasuk::orderBy('tanggal')->get();

        $results = [];

        foreach($logistik_masuk as $masuk){
            $results[] = [
                'id' => $masuk->id,
                'jenis_kebutuhan' => $masuk->jenis_kebutuhan,
                'keterangan' => $masuk->keterangan,
                'jumlah' => $masuk->jumlah,
                'status' => $masuk->status,
                'pengirim' => $masuk->pengirim,
                'satuan' => $masuk->satuan,
                'tanggal' => $masuk->tanggal,
                'foto' => $masuk->foto,
                'id_produk' => $masuk->id_produk,
                'nama_produk' => $masuk->produk->nama_produk,
            ];
        }
        
        return response()->json([
            'message' => 'Berhasil menampilkan logistik masuk',
            'status' => 200,
            'data' => $logistik_masuk
        ], 200);
    }

    public function tambahLogistikMasuk(Request $request){
        $rules = [
            'jenis_kebutuhan' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required',
            'status' => 'required',
            'pengirim' => 'required',
            'satuan' => 'required',
            'tanggal' => 'required',
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

        if($request->foto){
            $namaFoto = Carbon::now()->format('Y-m-d H:i:s')."-LogistikMasuk";
            $unggahFoto = $request->file('foto')->storeOnCloudinaryAs('Adit/LogistikMasuk', $namaFoto);

            $foto = $unggahFoto->getSecurePath();
            $publicId = $unggahFoto->getPublicId();
        }

        $id_produk = $request->id_produk;

        if($request->baru == 'baru'){
            $logistik = Logistik::create([
                'nama_produk' => $request->nama_produk,
                'id_posko' => auth()->user()->id_posko,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
            ]);

            $id_produk = $logistik->id;
        }else{
            $logistik = Logistik::where('id',$id_produk)->where('satuan', $request->satuan)->first();
            if($logistik){
                $logistik->update([
                    'jumlah' => (int)$logistik->jumlah + (int)$request->jumlah
                ]);
            }else{
                $logistik = Logistik::where('id',$id_produk)->first();
                Logistik::create([
                    'nama_produk' => $logistik->nama_produk,
                    'id_posko' => auth()->user()->id_posko,
                    'jumlah' => $request->jumlah,
                    'satuan' => $request->satuan,
                ]);
            }
            
        }

        $logistik_masuk = LogistikMasuk::create([
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'pengirim' => $request->pengirim,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal,
            'foto' => $request->foto? $foto : null,
            'public_id' => $request->foto? $publicId : null,
            'id_produk' => $id_produk
        ]);

        return response()->json([
            'message' => 'Berhasil menambahkan logistik masuk',
            'status' => 200,
            'data' => $logistik_masuk
        ], 200);
    }

    public function ubahLogistikMasuk(Request $request, $id){
        $logistik_masuk = LogistikMasuk::findOrFail($id);

        if($request->foto){
            $namaFoto = Carbon::now()->format('Y-m-d H:i:s')."-LogistikMasuk";
            $unggahFoto = $request->file('foto')->storeOnCloudinaryAs('Adit/LogistikMasuk', $namaFoto);

            $foto = $unggahFoto->getSecurePath();
            $publicId = $unggahFoto->getPublicId();
        }

        $logistik_masuk->update([
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->satuan,
            'pengirim' => $request->pengirim,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal,
            'foto' => $request->foto? $foto : $logistik_masuk->foto,
            'public_id' => $request->foto? $publicId : $logistik_masuk->public_id
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah logistik masuk',
            'status' => 200,
            'data' => $logistik_masuk
        ],200);
    }

    public function hapusLogistikMasuk($id){
        $logistik_masuk = LogistikMasuk::findOrFail($id);
        if($logistik_masuk->public_id != null){
            Cloudinary::destroy($logistik_masuk->public_id);
        }
        $logistik_masuk->delete();

        return response()->json([
            'message' => 'Berhasil menghapus logistik masuk',
            'status' => 200,
            'data' => $logistik_masuk
        ],200);
    }
}
