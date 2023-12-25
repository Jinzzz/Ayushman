<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagToMstStaffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_staffs', function (Blueprint $table) {
            $table->string('access_card_number')->after('last_login_time');
            $table->tinyInteger('is_resigned')->default(1)->comment('0: resigned, 1: not_resigned');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_staffs', function (Blueprint $table) {
            $table->dropColumn('access_card_number');
            $table->dropColumn('is_resigned');
        });
    }
}
