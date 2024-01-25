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
            $table->string('booking_reference_number');
            $table->date('invoice_date');
            $table->date('booking_date');
            $table->string('patient_name');
            $table->string('patient_contact');
            $table->string('payment_mode');
            $table->string('deposit_to');
            $table->string('reference_code');
            $table->string('amount');
            $table->string('discount_amount')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->string('due_amount')->nullable();
            $table->string('is_paid')->default(0);
            $table->integer('created_by');
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
