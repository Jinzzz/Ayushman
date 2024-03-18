<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStaffCashDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_staff_cash_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_from_account');
            $table->unsignedBigInteger('transfer_to_account');
            $table->unsignedBigInteger('branch_id');
            $table->decimal('transfer_amount',16,3);
            $table->date('transfer_date');
            $table->string('reference_number')->nullable();
            $table->string('remarks')->nullable();
            $table->string('created_by',20)->nullable();
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
        Schema::dropIfExists('trn_staff_cash_deposits');
    }
}
