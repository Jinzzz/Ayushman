<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStaffAdvanceSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__staff__advance__salaries', function (Blueprint $table) {
            $table->id();
            $table->date('salary_month');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('branch_id');
            $table->date('payed_date');
            $table->decimal('net_earnings');
            $table->decimal('paid_amount');
            $table->unsignedBigInteger('payment_mode');
            $table->unsignedBigInteger('payed_through_mode');
            $table->unsignedBigInteger('payed_through_ledger_id');
            $table->string('reference_number',200);
            $table->string('remarks',500);
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
        Schema::dropIfExists('trn__staff__advance__salaries');
    }
}
