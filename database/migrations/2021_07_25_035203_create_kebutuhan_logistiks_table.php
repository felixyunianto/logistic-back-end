<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKebutuhanLogistiksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kebutuhan_logistiks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_posko')->unsigned();
            $table->bigInteger('id_produk')->unsigned();
            $table->enum('jenis_kebutuhan', ['sandang','pangan', 'obat obatan', 'paket kematian', 'logistik lainya']);
            $table->text('keterangan');
            $table->integer('jumlah');
            $table->enum('status', ['Terpenuhi', 'Belum Terpenuhi']);
            $table->enum('satuan', ['kg', 'liter','dus','unit','buah','ram','lembar','pasang','bungkus','karung','kodi','pak']);
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('id_posko')->references('id')->on('poskos')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('id_produk')->references('id')->on('logistiks')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kebutuhan_logistiks');
    }
}
