<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->string('company_name');
            $table->bigInteger('status');
            $table->text('remark');
            $table->bigInteger('salary_head_id');
            $table->bigInteger('salary_head_type_id');
            $table->string('package_amount_type');
            $table->string('package_amount_value');
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
        Schema::dropIfExists('salary_packages');
    }
}
