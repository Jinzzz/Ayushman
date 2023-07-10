<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStaffLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_staff_leaves', function (Blueprint $table) {
            $table->bigIncrements('leave_id');
            $table->integer('user_id')->nullable();
            $table->integer('leave_type_id')->nullable();
            $table->integer('leave_duration')->nullable();
            $table->text('leave_reason')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('trn_staff_leaves');
    }
}
