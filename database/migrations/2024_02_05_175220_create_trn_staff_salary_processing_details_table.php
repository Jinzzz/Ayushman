<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStaffSalaryProcessingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_staff_salary_processing_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_processing_id');
            $table->unsignedBigInteger('salary_head_id');
            $table->decimal('amount', 16 , 3);
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
        Schema::dropIfExists('trn_staff_salary_processing_details');
    }
}
