<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', 'AuthController@login');
Route::post('/save-token', 'AuthController@saveToken');

//Posko
Route::get('/posko', 'PoskoController@infoPosko');
Route::post('/posko', 'PoskoController@tambahPosko');
Route::put('/posko/{id}', 'PoskoController@ubahPosko');
Route::delete('/posko/{id}', 'PoskoController@hapusPosko');

//Bencana
Route::get('/bencana', 'BencanaController@infoBencana');
Route::post('/bencana', 'BencanaController@tambahBencana');
Route::put('/bencana/{id}', 'BencanaController@ubahBencana');
Route::delete('/bencana/{id}', 'BencanaController@hapusBencana');

//Petugas
Route::get('/petugas-posko', 'PetugasController@infoPetugas');
Route::post('/petugas-posko', 'PetugasController@tambahPetugas');
Route::put('/petugas-posko/{id}', 'PetugasController@ubahPetugas');
Route::delete('/petugas-posko/{id}', 'PetugasController@hapusPetugas');

//Donatur
Route::get('/donatur', 'DonaturController@infoDonatur');
Route::post('/donatur', 'DonaturController@tambahDonatur');
Route::put('/donatur/{id}', 'DonaturController@ubahDonatur');
Route::delete('/donatur/{id}', 'DonaturController@hapusDonatur');

//Logistik Produk
Route::get('/logistik-produk', 'LogistikController@infoLogistik');
Route::post('/logistik-produk', 'LogistikController@tambahLogistik');
Route::put('/logistik-produk/{id}', 'LogistikController@ubahLogistik');
Route::delete('/logistik-produk/{id}', 'LogistikController@hapusLogistik');

//Logistik Masuk 
Route::get('/logistik-masuk', 'LogistikMasukController@infoLogistikMasuk');
Route::post('/logistik-masuk', 'LogistikMasukController@tambahLogistikMasuk');
Route::put('/logistik-masuk/{id}', 'LogistikMasukController@ubahLogistikMasuk');
Route::delete('/logistik-masuk/{id}', 'LogistikMasukController@hapusLogistikMasuk');

//Logistik Keluar
Route::get('/logistik-keluar', 'LogistikKeluarController@infoLogistikKeluar');
Route::get('/logistik-keluar/penerimaan', 'LogistikKeluarController@keluarByPenerima');
Route::post('/logistik-keluar', 'LogistikKeluarController@tambahLogistikKeluar');
Route::put('/logistik-keluar/{id}', 'LogistikKeluarController@ubahLogistikKeluar');
Route::delete('/logistik-keluar/{id}', 'LogistikKeluarController@hapusLogistikKeluar');
Route::post('/logistik-keluar/status/{id}', 'LogistikKeluarController@terimaLogistikKeluar');

//Penerimaan
Route::get('/penerimaan', 'PenerimaanLogistikController@infoPenerimaan');
Route::post('/penerimaan/keluar/{id}', 'PenerimaanLogistikController@tambahPenerimaan');
// Route::put('/penerimaan/{id}', 'PenerimaanLogistikController@ubahLogistikKeluar');
// Route::delete('/penerimaan/{id}', 'PenerimaanLogistikController@hapusLogistikKeluar');
// Route::post('/penerimaan/status/{id}', 'PenerimaanLogistikController@terimaLogistikKeluar');

//Penyaluran
Route::get('/penyaluran', 'PenyaluranLogistikController@infoPenyaluran');
Route::post('/penyaluran', 'PenyaluranLogistikController@tambahPenyaluran');

//Kebutuhan
Route::get('/kebutuhan-logistik', 'KebutuhanLogistikController@infoKebutuhanLogistik');
Route::get('/kebutuhan-logistik/posko/', 'KebutuhanLogistikController@infoKebutuhanLogistikByPosko');
Route::post('/kebutuhan-logistik', 'KebutuhanLogistikController@tambahKebutuhanLogistik');