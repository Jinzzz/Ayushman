<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnMedicineSalesInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__medicine__sales__invoices', function (Blueprint $table) {
            $table->bigIncrements('sales_invoice_id')->primary(); // Changing to bigIncrements as the primary key
            $table->string('sales_invoice_number', 100);
            $table->bigInteger('patient_id');
            $table->string('booking_id', 100)->nullable();
            $table->date('invoice_date');
            $table->bigInteger('branch_id');
            $table->bigInteger('sales_person_id');
            // $table->integer('manufacturer_id');
            $table->string('notes', 500);
            $table->string('terms_and_conditions', 500);
            $table->decimal('sub_total', 16, 3);
            $table->decimal('total_tax_amount', 16, 3);
            $table->decimal('total_amount', 16, 3);
            $table->decimal('discount_amount', 16, 3);
            $table->decimal('payable_amount', 16, 3);
            $table->integer('financial_year_id')->nullable();
            $table->integer('deposit_to');
            $table->integer('payment_mode');
            $table->tinyInteger('is_deleted')->default(1)->comment('0: not deleted, 1: deleted');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->bigInteger('deleted_by');
            $table->timestamps(); // Adding timestamps

            $table->foreign('created_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('mst_users')->onDelete('set null');

            $table->foreign('branch_id')->references('branch_id')->on('mst_branches')->onDelete('set null');

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__medicine__sales__invoices');
    }
}
