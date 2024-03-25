<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnMedicineSalesReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__medicine__sales__return__details', function (Blueprint $table) {
            $table->bigInteger('sales_return_details_id')->primary(); 
            $table->bigInteger('sales_return_id');
            $table->bigInteger('medicine_id');
            $table->bigInteger('batch_id');
            $table->integer('quantity_unit_id');
            $table->decimal('quantity', 14, 2);
            $table->decimal('rate', 14, 2);
            $table->decimal('discount', 14, 2);
            $table->decimal('tax_value', 5, 2);
            $table->decimal('tax_amount', 14, 2);
            $table->decimal('amount', 14, 2);
            $table->timestamps();

            $table->foreign('sales_return_id')->references('sales_return_id')->on('trn__medicine__sales__returns')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('mst_medicines')->onDelete('cascade');
            // $table->foreign('stock_id')->references('stock_id')->on('trn_medicine_stock')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__medicine__sales__return__details');
    }
}
