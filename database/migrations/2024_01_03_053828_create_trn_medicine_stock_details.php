<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnMedicineStockDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_medicine_stock_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('batch_id')->nullable();
            $table->bigInteger('unit_id')->nullable();
            $table->string('sales_rate')->nullable();
            $table->string('mrp')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_medicine_stock_details');
    }
}
