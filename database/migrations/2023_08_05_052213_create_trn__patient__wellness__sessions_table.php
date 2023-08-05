<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPatientWellnessSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__patient__wellness__sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membership_patient_id');
            $table->unsignedBigInteger('wellness_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_patient_id')->references('membership_patient_id')->on('mst__patient__membership__bookings')->onDelete('cascade');
            $table->foreign('wellness_id')->references('id')->on('mst_wellness')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__patient__wellness__sessions');
    }
}
