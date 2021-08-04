<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\LogistikMasuk;
use App\LogistikKeluar;
use App\PenerimaanLogistik;
use App\PenyaluranLogistik;

class PdfController extends Controller
{
    public function logistikMasuk(Request $request){
        $logistik_masuk = LogistikMasuk::orderBy('tanggal')->get();
        if($request->tanggal_awal){
            $logistik_masuk = LogistikMasuk::whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])->orderBy('tanggal')->get();
        }
        $pdf = PDF::loadview('pdf.logistik-masuk', compact('logistik_masuk'));
        return $pdf->stream();
    }

    public function logistiKeluar(Request $request, $id_posko){
        $logistik_keluar = LogistikKeluar::with('posko_penerima', 'produk')
            ->where('pengirim_id',$id_posko)
            ->orderBy('created_at', 'DESC')
            ->get();
        if($request->tanggal_awal){
            $logistik_keluar = LogistikKeluar::with('posko_penerima', 'produk')
            ->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])
            ->where('pengirim_id',$id_posko)
            ->orderBy('created_at', 'DESC')
            ->get();
        }
        $pdf = PDF::loadview('pdf.logistik-keluar', compact('logistik_keluar'));
        return $pdf->stream();
    }

    public function penerimaanLogistik(Request $request, $id_posko){
        $penerimaan = PenerimaanLogistik::with('produk')
            ->orderBy('tanggal','DESC')->where('penerima_id', $id_posko)->get();
        if($request->tanggal_awal){
            $penerimaan = PenerimaanLogistik::with('produk')
                ->orderBy('tanggal','DESC')
                ->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])
                ->where('penerima_id', $id_posko)
                ->get();
        }

        $pdf = PDF::loadview('pdf.penerimaan', compact('penerimaan'));
        return $pdf->stream();

    }

    public function penyaluranLogistik(Request $request, $id_posko){
        $penyaluran = PenyaluranLogistik::with('produk')->where('pengirim_id', $id_posko)->get();

        if($request->tanggal_awal){
            $penyaluran = PenyaluranLogistik::with('produk')
                ->orderBy('tanggal','DESC')
                ->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir])
                ->where('pengirim_id', $id_posko)
                ->get();
        }

        $pdf = PDF::loadview('pdf.penyaluran', compact('penyaluran'));
        return $pdf->stream();
    }
}
