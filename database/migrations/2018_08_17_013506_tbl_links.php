<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Illuminate\Support\Facades\DB::setDefaultConnection('inspections');
        Schema::create('tbl_links', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('area_id')->unsigned();
            $table->foreign('area_id')->references('id')->on('tbl_areas');

            $table->integer('component_id')->unsigned();
            $table->foreign('component_id')->references('id')->on('tbl_components');

            $table->integer('standard_id')->unsigned();
            $table->foreign('standard_id')->references('id')->on('tbl_standards');

            $table->integer('remarks_id')->unsigned();
            $table->foreign('remarks_id')->references('id')->on('tbl_remarks');

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
        Schema::dropIfExists('tbl_links');
    }
}
