<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnIncomeExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_income_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('income_expense_type_id');
            $table->date('income_expense_date');
            $table->string('income_expense_ledger_id');
            $table->decimal('income_expense_amount', 16, 3);
            $table->tinyInteger('transaction_mode_id');
            $table->string('transaction_account_id');
            $table->string('reference',500)->nullable();
            $table->string('notes',500)->nullable();
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
        Schema::dropIfExists('trn_income_expenses');
    }
}
