<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bencana;
use App\User;
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

        $bencanaExists = Bencana::where('nama', $request->nama)->first();
        if($bencanaExists != null){
            return response()->json([
                'message' => 'Gagal menambahkan data bencana',
                'status' => 200,
                'error' => 'Nama bencana sudah ada'
            ]);
        }

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

        $deviceToken = User::whereNotNull('device_token')->pluck('device_token')->all();

        $this->sendNotification("Pemberitahuan", 
            "Terjadi bencana " . $bencana->nama." pada tanggal ". Carbon::parse($bencana->tanggal)->format("d M Y"),
        $deviceToken);

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
