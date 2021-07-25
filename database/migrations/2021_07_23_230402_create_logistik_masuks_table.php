<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogistikMasuksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistik_masuks', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_kebutuhan', ['sandang','pangan', 'obat obatan', 'paket kematian', 'logistik lainya']);
            $table->text('keterangan');
            $table->integer('jumlah');
            $table->enum('status', ['Proses', 'Terima'])->default('Terima');
            $table->string('pengirim', 50);
            $table->enum('satuan', ['kg', 'liter','dus','unit','buah','ram','lembar','pasang','bungkus','karung','kodi','pak']);
            $table->date('tanggal');
            $table->text('foto')->nullable();
            $table->text('public_id')->nullable();
            $table->bigInteger('id_produk')->unsigned();
            $table->timestamps();

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
        Schema::dropIfExists('logistik_masuks');
    }
}
