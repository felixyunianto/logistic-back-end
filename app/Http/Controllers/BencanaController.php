<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bencana;
use Cloudinary;
use Carbon\Carbon;
use Validator;

class BencanaController extends Controller
{
    public function __construct(){
        $this->middleware("auth:api")->except('infoBencana');
    }

    public function infoBencana(){
        $bencana = Bencana::orderBy('created_at')->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data bencana',
            'status' => 200,
            'data' => $bencana
        ],200);
    }

    public function tambahBencana(Request $request){
        $rules = [
            'nama' => 'required',
            'detail' => 'required',
            'tanggal' => 'required',
        ];

        $messages = [
            'required' => ':attribute harus diisi'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);        

        if($request->foto){
            $namaFoto = Carbon::now()->format('Y-m-d H:i:s')."-".$request->nama;
            $unggahFoto = $request->file('foto')->storeOnCloudinaryAs('Adit/Bencana', $namaFoto);

            $foto = $unggahFoto->getSecurePath();
            $publicId = $unggahFoto->getPublicId();
        }

        $bencana = Bencana::create([
            'nama' => $request->nama,
            'detail' => $request->detail,
            'tanggal' => $request->tanggal,
            'foto' => $request->foto ? $foto : null,
            'public_id' => $request->foto ? $publicId : null,
        ]);

        return response()->json([
            'message' => 'Berhasil menambahkan data bencana',
            'status' => 200,
            'data' => $bencana
        ],200);
    }

    public function ubahBencana(Request $request, $id){
        $bencana = Bencana::findOrFail($id);

        if($request->foto){
            Cloudinary::destroy($bencana->public_id);

            $namaFoto = Carbon::now()->format('Y-m-d H:i:s')."-".$request->nama;
            $unggahFoto = $request->file('foto')->storeOnCloudinaryAs('Adit/Bencana', $namaFoto);

            $foto = $unggahFoto->getSecurePath();
            $publicId = $unggahFoto->getPublicId();
        }

        $bencana->update([
            'nama' => $request->nama,
            'detail' => $request->detail,
            'tanggal' => $request->tanggal,
            'foto' => $request->foto ? $foto : $bencana->foto,
            'public_id' => $request->foto ? $publicId : $bencana->public_id,
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data bencana',
            'status' => 200,
            'data' => $bencana
        ],200);
    }

    public function hapusBencana($id){
        $bencana = Bencana::findOrFail($id);
        Cloudinary::destroy($bencana->public_id);
        $bencana->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data bencana',
            'status' => 200,
            'data' => $bencana
        ],200);

    }
}
