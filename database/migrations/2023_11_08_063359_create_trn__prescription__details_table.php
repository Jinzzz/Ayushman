<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPrescriptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__prescription__details', function (Blueprint $table) {
            $table->bigIncrements('prescription_details_id'); // Primary Key
            $table->unsignedBigInteger('priscription_id'); // Foreign key
            $table->unsignedBigInteger('medicine_id'); // Foreign key
            $table->integer('duration')->comment('in days');
            $table->string('medicine_dosage', 100);
            $table->text('remarks');
            $table->timestamps();
            
            // Define foreign key constraints
            $table->foreign('priscription_id')->references('prescription_id')->on('trn__prescriptions');
            $table->foreign('medicine_id')->references('id')->on('mst_medicines');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__prescription__details');
    }
}
