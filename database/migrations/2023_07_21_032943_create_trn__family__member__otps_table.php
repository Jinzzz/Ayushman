<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnFamilyMemberOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__family__member__otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('family_member_id');
            $table->string('otp');
            $table->integer('verified');
            $table->datetime('otp_expire_at');

            $table->foreign('patient_id')->references('id')->on('mst_users')->onDelete('cascade');
            $table->foreign('family_member_id')->references('id')->on('trn_patient_family_member')->onDelete('cascade');
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
        Schema::dropIfExists('trn__family__member__otps');
    }
}
