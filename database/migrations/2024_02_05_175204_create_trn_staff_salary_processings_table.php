<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStaffSalaryProcessingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_staff_salary_processings', function (Blueprint $table) {
            $table->id();
            $table->date('salary_month');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('branch_id');
            $table->date('processed_date');
            $table->unsignedBigInteger('account_ledger_id');
            $table->decimal('bonus, 16, 3');
            $table->decimal('overtime_allowance, 16, 3');
            $table->decimal('other_earnings, 16, 3');
            $table->decimal('other_deductions, 16, 3');
            $table->decimal('lop, 16, 3');
            $table->decimal('total_earnings, 16, 3');
            $table->decimal('total_deductions, 16, 3');
            $table->decimal('net_earning, 16, 3');
            $table->unsignedBigInteger('payment_mode');
            $table->string('reference_number',200);
            $table->string('remarks',500);
            $table->tinyInteger('processing_status')->nullable();
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
        Schema::dropIfExists('trn_staff_salary_processings');
    }
}
