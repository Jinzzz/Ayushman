<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstMembershipPackageWellnessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__membership__package__wellnesses', function (Blueprint $table) {
            $table->unsignedBigInteger('package_wellness_id')->primary();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('wellness_id');
            $table->integer('maximum_usage_limit');
            $table->tinyInteger('is_active')->default(0)->comment('0: not active, 1: active');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('package_id')->references('membership_package_id')->on('mst__membership__packages')->onDelete('cascade');
            $table->foreign('wellness_id')->references('id')->on('mst_wellness')->onDelete('cascade');
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
        Schema::dropIfExists('mst__membership__package__wellnesses');
    }
}
