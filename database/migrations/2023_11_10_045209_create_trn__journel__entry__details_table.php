<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnJournelEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__journel__entry__details', function (Blueprint $table) {
            $table->bigIncrements('journel_entry_details_id');
            $table->bigInteger('journal_entry_id');
            $table->bigInteger('account_ledger_id');
            $table->decimal('debit', 14, 2);
            $table->decimal('credit', 14, 2);
            $table->string('description', 500);
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('journal_entry_id')->on('trn__journel__entries')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__journel__entry__details');
    }
}
