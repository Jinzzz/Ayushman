<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnJournelEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__journel__entries', function (Blueprint $table) {
            $table->bigIncrements('journal_entry_id');
            $table->bigInteger('Journel_entry_type_id');
            $table->string('Journel_number', 100);
            $table->date('Journel_date');
            $table->bigInteger('branch_id');
            $table->integer('financial_year_id');
            $table->string('notes', 500);
            $table->decimal('total_debit', 14, 2);
            $table->decimal('total_credit', 14, 2);
            $table->boolean('is_deleted');
            $table->bigInteger('created_by');
            $table->bigInteger('deleted_by');
            $table->date('deleted_at');
            $table->timestamps();
            
            $table->foreign('created_by')->references('user_id')->on('mst_users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('user_id')->on('mst_users')->onDelete('set null');
            $table->foreign('branch_id')->references('branch_id')->on('mst_branches')->onDelete('cascade');
            $table->foreign('Journel_entry_type_id')->references('journal_entry_type_id')->on('mst__journel__entry__types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__journel__entries');
    }
}
