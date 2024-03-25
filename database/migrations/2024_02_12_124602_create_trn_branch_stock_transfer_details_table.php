<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnBranchStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_branch_stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('stock_id');
            $table->string('transfered_quantity');
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
        Schema::dropIfExists('trn_branch_stock_transfer_details');
    }
}
