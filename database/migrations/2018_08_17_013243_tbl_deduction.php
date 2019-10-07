<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblDeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Illuminate\Support\Facades\DB::setDefaultConnection('inspections');
        Schema::create('tbl_deduction', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('remarks_id')->unsigned();
            $table->foreign('remarks_id')->references('id')->on('tbl_remarks');

            $table->integer('tbl_findings_type_id')->unsigned();
            $table->foreign('tbl_findings_type_id')->references('id')->on('tbl_findings_type');

            $table->integer('points');

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
        Schema::dropIfExists('tbl_deduction');
    }
}
