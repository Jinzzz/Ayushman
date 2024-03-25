<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBillableToTrnConsultationBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_consultation_bookings', function (Blueprint $table) {
            $table->tinyInteger('is_billable')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_consultation_bookings', function (Blueprint $table) {
            $table->dropColumn('is_billable');
        });
    }
}
