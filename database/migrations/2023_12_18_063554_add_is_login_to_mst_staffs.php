<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLoginToMstStaffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_staffs', function (Blueprint $table) {
            $table->tinyInteger('is_login')->default(0)->comment('0: not_login, 1: login');
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
            $table->dropColumn('is_login');
        });
    }
}
