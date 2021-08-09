<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KebutuhanLogistik;
use App\User;
use Validator;
use Auth;

class KebutuhanLogistikController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except('infoKebutuhanLogistik', 'infoKebutuhanLogistikByPosko');
    }

    public function infoKebutuhanLogistik(){
        $data_kebutuhan_logistik = KebutuhanLogistik::with('posko', 'produk')->get();

        $results = [];

        foreach($data_kebutuhan_logistik as $kebutuhan){
            $results[] = [
                'id' => $kebutuhan->id,
                'id_posko' => $kebutuhan->id_posko,
                'posko' => $kebutuhan->posko->nama,
                'jenis_kebutuhan' => $kebutuhan->jenis_kebutuhan,
                'keterangan' => $kebutuhan->keterangan,
                'jumlah' => $kebutuhan->jumlah,
                'status' => $kebutuhan->status,
                'satuan' => $kebutuhan->satuan,
                'tanggal' => $kebutuhan->tanggal,
                'id_produk' => $kebutuhan->id_produk,
                'produk' => $kebutuhan->produk->nama_produk,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan kebutuhan logistik',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function infoKebutuhanLogistikByPosko($id_posko){
        $data_kebutuhan_logistik = KebutuhanLogistik::where('id_posko',$id_posko)->get();
        
        $results = [];

        foreach($data_kebutuhan_logistik as $kebutuhan){
            $results[] = [
                'id' => $kebutuhan->id,
                'id_posko' => $kebutuhan->id_posko,
                'posko' => $kebutuhan->posko->nama,
                'jenis_kebutuhan' => $kebutuhan->jenis_kebutuhan,
                'keterangan' => $kebutuhan->keterangan,
                'jumlah' => $kebutuhan->jumlah,
                'status' => $kebutuhan->status,
                'satuan' => $kebutuhan->satuan,
                'tanggal' => $kebutuhan->tanggal,
                'id_produk' => $kebutuhan->id_produk,
                'produk' => $kebutuhan->produk->nama_produk,
            ];
        }


        return response()->json([
            'message' => 'Berhasil menampilkan kebutuhan logistik',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function tambahKebutuhanLogistik(Request $request){
        $rules = [  
            'jenis_kebutuhan' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required',
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
            'id_posko' => Auth::user()->id_posko,
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => 'Belum Terpenuhi',
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal
        ]);

        $deviceToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        // dd($deviceToken);

        $this->sendNotification("Pemberitahuan", 
        $data_kebutuhan_logistik->posko->nama." membutuhkan " .$data_kebutuhan_logistik->produk->nama_produk." ".$data_kebutuhan_logistik->jumlah." ".$data_kebutuhan_logistik->satuan, 
        $deviceToken);

        return response()->json([
            'message' => 'Berhasil menambahkan data kebutuhan logistik',
            'status' => 200
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

    public function sendNotification($title, $body, $token){
        $data = [
            'title' => $title,
            'body' => $body,
        ];

        $device_token = [];
        
        foreach($token as $t){
            // dd($t);
            $device_token[] = $t;
        }

        // dd($device_token);

        $payload = [
            'registration_ids' => $device_token,
            'notification' => $data
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json",
                "Authorization: key=".env('FIREBASE_SERVER_KEY')
            ),
        ));

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($curl);
        curl_close($curl);
        
        return response()->json([
            'message' => 'Berhasil mengirim notif',
            'status' => 200,
            'data' => json_encode($response)
        ], 200);
    }
}
