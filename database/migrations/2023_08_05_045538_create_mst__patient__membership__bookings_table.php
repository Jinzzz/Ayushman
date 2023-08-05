<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstPatientMembershipBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__patient__membership__bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_patient_id')->primary();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('membership_package_id');
            $table->datetime('membership_expiry_date');
            $table->string('payment_type')->default(0)->comment('0: cash, 1: liquid');;
            $table->string('payment_amount');
            $table->string('details');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_package_id')->references('membership_package_id')->on('mst__membership__packages')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('mst_patients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__patient__membership__bookings');
    }
}
