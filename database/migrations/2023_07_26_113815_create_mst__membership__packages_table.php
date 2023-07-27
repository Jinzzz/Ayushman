<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstMembershipPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__membership__packages', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_package_id')->primary();
            $table->string('package_title');
            $table->string('package_duration');
            $table->text('package_description');
            $table->decimal('package_price', 10, 2); 
            $table->decimal('package_discount_price', 10, 2); 
            $table->tinyInteger('is_active')->default(0)->comment('0: not active, 1: active'); 
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__membership__packages');
    }
}
