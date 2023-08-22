<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPatientDeviceTockensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__patient__device__tockens', function (Blueprint $table) {
            $table->bigIncrements('patient_device_token_id', 191)->primary();
            $table->unsignedBigInteger('patient_id');
            $table->text('patient_device_token');
            $table->text('patient_device_type');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('mst_patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__patient__device__tockens');
    }
}
