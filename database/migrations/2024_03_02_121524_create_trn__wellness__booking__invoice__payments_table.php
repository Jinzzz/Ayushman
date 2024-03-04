<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnWellnessBookingInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__wellness__booking__invoice__payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wellness_booking_invoice_id');
            $table->decimal('paid_amount',16,3);
            $table->string('payment_mode');
            $table->string('deposit_to');
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
        Schema::dropIfExists('trn__wellness__booking__invoice__payments');
    }
}
