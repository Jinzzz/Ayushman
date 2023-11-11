<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnMedicineSalesReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__medicine__sales__returns', function (Blueprint $table) {
            $table->bigInteger('sales_return_id')->primary()->unique();
            $table->string('sales_return_no', 100)->unique();
            $table->bigInteger('sales_invoice_id');
            $table->bigInteger('patient_id');
            $table->date('return_date');
            $table->integer('branch_id');
            $table->decimal('sub_total', 14, 2);
            $table->decimal('total_discount', 14, 2);
            $table->decimal('total_tax', 14, 2);
            $table->decimal('total_amount', 14, 2);
            $table->string('notes', 500);
            $table->tinyInteger('is_deleted')->default(1)->comment('0: not deleted, 1: deleted');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->bigInteger('deleted_by');
            $table->datetime('deleted_on');
            $table->timestamps();

            $table->foreign('created_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('mst_users')->onDelete('set null');
            $table->foreign('sales_invoice_id')->references('sales_invoice_id')->on('trn_medicine_sale_invoices')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('mst_patients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__medicine__sales__returns');
    }
}
