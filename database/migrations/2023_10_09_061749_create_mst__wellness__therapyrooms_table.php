<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstWellnessTherapyroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__wellness__therapyrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wellness_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('therapy_room_id');
            $table->timestamps();

            $table->foreign('wellness_id')->references('wellness_id')->on('mst_wellness')->onDelete('cascade');   
            $table->foreign('branch_id')->references('branch_id')->on('mst_branches')->onDelete('cascade');
            $table->foreign('therapy_room_id')->references('id')->on('mst_therapy_rooms')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__wellness__therapyrooms');
    }
}
