<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__feedback', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id');
            $table->unsignedBigInteger('consultancy_rating');
            $table->unsignedBigInteger('visit_rating');
            $table->unsignedBigInteger('service_rating');
            $table->unsignedBigInteger('pharmacy_rating');
            $table->unsignedBigInteger('appointment_rating');
            $table->string('average_rating');
            $table->string('feedback');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('trn__feedback');
    }
}
