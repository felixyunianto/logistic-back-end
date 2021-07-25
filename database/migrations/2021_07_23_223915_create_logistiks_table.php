<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogistiksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk', 50);
            $table->bigInteger('id_posko')->unsigned();
            $table->integer('jumlah');
            $table->enum('satuan', ['kg', 'liter','dus','unit','buah','ram','lembar','pasang','bungkus','karung','kodi','pak']);
            $table->timestamps();

            $table->foreign('id_posko')->references('id')->on('poskos')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistiks');
    }
}
