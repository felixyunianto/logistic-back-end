<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\LogistikMasuk;

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
}
