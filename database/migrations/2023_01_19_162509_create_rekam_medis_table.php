<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->nullable()->constrained()->on('pasien')->onUpdate('cascade');
            $table->text('keluhan')->nullable();
            $table->foreignId('dokter_id')->nullable()->constrained()->on('dokter')->onUpdate('cascade');
            $table->text('diagnosa')->nullable();
            $table->text('perawatan')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('rekam_medis');
    }
};
