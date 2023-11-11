<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstTherapyRoomSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__therapy__room__slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('therapy_room_id');
            $table->unsignedBigInteger('week_day');
            $table->unsignedBigInteger('timeslot');
            $table->tinyInteger('is_active')->default(1)->comment('0: not active, 1: active');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('therapy_room_id')->references('id')->on('mst_therapy_rooms')->onDelete('cascade');
            $table->foreign('week_day')->references('id')->on('mst_master_values')->onDelete('cascade');
            $table->foreign('timeslot')->references('id')->on('mst_master_values')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('mst_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__therapy__room__slots');
    }
}
