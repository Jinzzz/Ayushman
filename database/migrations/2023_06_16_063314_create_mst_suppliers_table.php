<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code',50);
            $table->string('supplier_name',100);
            $table->string('supplier_contact',20);
            $table->string('supplier_email',200);
            $table->string('supplier_address',250);
            $table->string('gstno',20);
            $table->string('remarks',250);
            $table->boolean('is_active');
            $table->boolean('is_deleted');
            $table->integer('deleted_by');
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
        Schema::dropIfExists('mst_suppliers');
    }
}
