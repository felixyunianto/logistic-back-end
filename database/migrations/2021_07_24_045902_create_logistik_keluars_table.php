<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogistikKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistik_keluars', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_kebutuhan', ['sandang','pangan', 'obat obatan', 'paket kematian', 'logistik lainya']);
            $table->text('keterangan');
            $table->integer('jumlah');
            $table->enum('status', ['Proses', 'Terima'])->default('Terima');
            $table->bigInteger('pengirim_id')->unsigned();
            $table->enum('satuan', ['kg', 'liter','dus','unit','buah','ram','lembar','pasang','bungkus','karung','kodi','pak']);
            $table->date('tanggal');
            $table->bigInteger('id_produk')->unsigned();
            $table->bigInteger('penerima_id')->unsigned();
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('logistiks')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('penerima_id')->references('id')->on('poskos')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('pengirim_id')->references('id')->on('poskos')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistik_keluars');
    }
}
