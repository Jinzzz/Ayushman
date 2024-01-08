<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPaidToTrnMedicinePurchaseInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_medicine_purchase_invoices', function (Blueprint $table) {
            $table->tinyInteger('is_paid')->default(0)->comment('0: not_paid, 1: paid')->after('reference_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_medicine_purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
}
