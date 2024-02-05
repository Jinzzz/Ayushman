<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnBillingInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('booking_invoice_number');
            $table->date('invoice_date');
            $table->date('booking_date');
            $table->string('patient_name');
            $table->string('patient_contact');
            $table->string('paid_amount');
            $table->string('due_amount');
            $table->string('is_paid')->default(0);
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
        Schema::dropIfExists('trn_billing_invoices');
    }
}
