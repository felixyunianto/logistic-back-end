<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LogistikKeluar;
use App\Logistik;
use App\User;

class LogistikKeluarController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }

    public function infoLogistikKeluar(){
        $logistik_keluar = LogistikKeluar::with('posko_penerima', 'produk')
            ->orderBy('created_at', 'DESC')
            ->get();

        $results = [];

        foreach ($logistik_keluar as $keluar){
            $results[] = [
                'id' => $keluar->id,
                'jenis_kebutuhan' => $keluar->jenis_kebutuhan,
                'keterangan' => $keluar->keterangan,
                'jumlah' => $keluar->jumlah,
                'status' => $keluar->status,
                'pengirim_id' => $keluar->pengirim_id,
                'pengirim' => $keluar->posko_pengirim->nama,
                'satuan' => $keluar->satuan,
                'tanggal' => $keluar->tanggal,
                'id_produk' => $keluar->id_produk,
                'penerima_id' => $keluar->penerima_id,
                'penerima' => $keluar->posko_penerima->nama,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data logistik keluar',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function keluarByPenerima(){
        $logistik_keluar = LogistikKeluar::with('posko_penerima', 'produk')
            ->where('penerima_id', auth()->user()->id_posko)
            ->where('status', 'Proses')
            ->orderBy('created_at', 'DESC')
            ->get();

        $results = [];

        foreach ($logistik_keluar as $keluar){
            $results[] = [
                'id' => $keluar->id,
                'jenis_kebutuhan' => $keluar->jenis_kebutuhan,
                'keterangan' => $keluar->keterangan,
                'jumlah' => $keluar->jumlah,
                'status' => $keluar->status,
                'pengirim_id' => $keluar->pengirim_id,
                'pengirim' => $keluar->posko_pengirim->nama,
                'satuan' => $keluar->satuan,
                'tanggal' => $keluar->tanggal,
                'id_produk' => $keluar->id_produk,
                'penerima_id' => $keluar->penerima_id,
                'penerima' => $keluar->posko_penerima->nama,
            ];
        }

        return response()->json([
            'message' => 'Berhasil menampilkan data logistik keluar',
            'status' => 200,
            'data' => $results
        ],200);
    }

    public function tambahLogistikKeluar(Request $request){
        $produk = Logistik::findOrFail($request->id_produk);
        if((int)$produk->jumlah - (int)$request->jumlah < 0){
            return response()->json([
                'message' => 'Jumlah barang tidak cukup',
                'status' => 409,
            ], 409);
        }

        $logistik_keluar = LogistikKeluar::create([
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'pengirim_id' => auth()->user()->id_posko,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal,
            'id_produk' => $request->id_produk,
            'penerima_id' => $request->penerima_id,
        ]);

        $produk->update(['jumlah' => (int)$produk->jumlah - (int)$request->jumlah]);

        return response()->json([
            'message' => 'Berhasil menambahkan data logistik keluar',
            'status' => 200,
            'data' => $logistik_keluar
        ],200);
    }

    public function ubahLogistikKeluar(Request $request, $id){
        $produk = Logistik::findOrFail($request->id_produk);
        $logistik_keluar = LogistikKeluar::findOrFail($id);
        $logistik_keluar->update([
            'jenis_kebutuhan' => $request->jenis_kebutuhan,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
            'pengirim_id' => auth()->user()->id_posko,
            'satuan' => $request->satuan,
            'tanggal' => $request->tanggal,
            'id_produk' => $request->id_produk,
            'penerima_id' => $request->penerima_id,
        ]);

        $produk->update([
            'jumlah' => $logistik_keluar->jumlah > $request->jumlah 
            ? (int)$produk->jumlah + ((int) $logistik_keluar->jumlah - (int)$request->jumlah) 
            : (int)$produk->jumlah - ((int)$request->jumlah - (int) $logistik_keluar->jumlah)
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah data logistik keluar',
            'status' => 200,
            'data' => $logistik_keluar
        ],200);
    }

    public function hapusLogistikKeluar($id){
        $logistik_keluar = LogistikKeluar::findOrFail($id);  
        $logistik_keluar->delete();

        return response()->json([
            'message' => 'Berhasil mengubah data logistik keluar',
            'status' => 200,
            'data' => $logistik_keluar
        ],200);
    }

    public function terimaLogistikKeluar($id){
        $logistik_keluar = LogistikKeluar::findOrFail($id);
        $logistik_keluar->update(['status' => 'Terima']);

        $deviceToken = User::whereNotNull('device_token')->pluck('device_token')->where('level','Admin')->all();
        // dd($deviceToken);

        $this->sendNotification("Pemberitahuan", 
        $data_kebutuhan_logistik->posko->nama." membutuhkan " .$data_kebutuhan_logistik->produk->nama_produk." ".$data_kebutuhan_logistik->jumlah." ".$data_kebutuhan_logistik->satuan, 
        $deviceToken);

        return response()->json([
            'message' => 'Berhasil mengubah status',
            'status' => 200,
        ], 200);
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
