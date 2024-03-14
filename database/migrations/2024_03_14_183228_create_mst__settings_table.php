<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_logo',500);
            $table->string('company_name');
            $table->string('company_address',500);
            $table->string('company_location');
            $table->string('company_email');
            $table->string('contact_number_1');
            $table->string('contact_number_2');
            $table->string('gst_number');
            $table->string('company_website_link');
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
        Schema::dropIfExists('mst__settings');
    }
}
