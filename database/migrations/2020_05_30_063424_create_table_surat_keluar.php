<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuratKeluar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tgl_terima');
            $table->string('asal_surat')->index();
            $table->string('nomor_surat')->index();
            $table->dateTime('tgl_surat');
            $table->string('perioritas')->index();
            $table->string('perihal')->nullable()->index();
            $table->string('keterangan')->nullable()->index();
            $table->boolean('tindak_lanjut')->default(false);
            $table->boolean('status')->default(false);
            $table->string('file_surat')->nullable();
            $table->integer('jenis_surat')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surat_keluar');
    }
}
