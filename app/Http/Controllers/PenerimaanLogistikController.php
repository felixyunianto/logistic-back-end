<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PenerimaanLogistik;
use App\LogistikKeluar;
use App\Logistik;
use App\User;
use Carbon\Carbon;

class PenerimaanLogistikController extends Controller
{
    public function __construct(){
        $this->middleware("auth:api")->except('penerimaanByPosko');
    }



    public function infoPenerimaan(){
        $penerimaan = PenerimaanLogistik::orderBy('tanggal','DESC')->where('penerima_id', auth()->user()->id_posko)->get();

        return response()->json([
            'message' => 'Berhasil menampilkan data penerimaan',
            'status' => 200,
            'data' => $penerimaan
        ]);
    }

    public function penerimaanByPosko($id_posko){
        $penerimaan = PenerimaanLogistik::orderBy('tanggal','DESC')->where('penerima_id', $id_posko)->get();

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


        $deviceToken = User::whereNotNull('device_token')->where('level','Admin')->pluck('device_token')->all();

        $this->sendNotification("Pemberitahuan", 
            $keluar->produk->nama_produk." telah diterima oleh ".$keluar->posko_penerima->nama, 
        $deviceToken);

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
            'tanggal' => Carbon::now()->format("Y-m-d"),
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
