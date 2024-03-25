<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnMedicineSalesInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__medicine__sales__invoice__details', function (Blueprint $table) {
            $table->bigIncrements('sales_invoice_details_id')->primary();
            $table->bigInteger('sales_invoice_id');
            $table->bigInteger('medicine_id');
            $table->integer('medicine_unit_id');
            $table->bigInteger('batch_id');
            $table->decimal('quantity', 14, 3);
            $table->decimal('rate', 16, 3);
            $table->decimal('amount', 16, 3);
            $table->date('expiry_date');
            $table->date('manufactured_date');
            $table->string('med_quantity_tax_amount', 100);
            $table->timestamps(); // Adding timestamps

            $table->foreign('sales_invoice_id')->references('sales_invoice_id')->on('trn__medicine__sales__invoices')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('mst_medicines')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__medicine__sales__invoice__details');
    }
}
