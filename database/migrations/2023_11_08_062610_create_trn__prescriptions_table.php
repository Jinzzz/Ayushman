<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__prescriptions', function (Blueprint $table) {
            $table->bigIncrements('prescription_id')->comment('Primary Key');
            $table->unsignedBigInteger('Booking_Id')->comment('Foreign key');
            $table->unsignedBigInteger('doctor_id')->comment('Foreign key');
            $table->integer('duration')->comment('in days');
            $table->timestamps();
            
            $table->foreign('Booking_Id')->references('id')->on('trn_consultation_bookings');
            $table->foreign('doctor_id')->references('staff_id')->on('mst_staffs');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__prescriptions');
    }
}
