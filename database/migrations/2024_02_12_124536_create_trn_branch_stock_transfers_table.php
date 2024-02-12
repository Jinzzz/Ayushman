<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnBranchStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_branch_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('transfer_code');
            $table->unsignedBigInteger('from_pharmacy_id');
            $table->unsignedBigInteger('to_pharmacy_id');
            $table->date('transfer_date');
            $table->unsignedBigInteger('from_branch_id');
            $table->unsignedBigInteger('to_branch_id');
            $table->string('notes,500');
            $table->string('reference_file, 500');
            $table->string('created_by',20)->nullable();
            $table->string('updated_by',20)->nullable();
            $table->string('deleted_by',20)->nullable();
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
        Schema::dropIfExists('trn_branch_stock_transfers');
    }
}
