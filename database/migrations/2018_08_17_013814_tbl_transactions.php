<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Illuminate\Support\Facades\DB::setDefaultConnection('inspections');
        Schema::create('tbl_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('links_id')->default(0);
            // $table->integer('links_id')->unsigned();
            // $table->foreign('links_id')->references('id')->on('tbl_links');
            $table->string('other_remarks')->nullable();
            $table->string('room_no');
            // $table->integer('room_id');
            // $table->foreign('room_id')->references('id')->on('hms_tblroom');
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
        Schema::dropIfExists('tbl_transactions');
    }
}
