<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tblroomgroupstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblroomgroupstatus', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('room_status_id')->unsigned();
            $table->foreign('room_status_id')->references('id')->on('roomstatus');

            $table->integer('available_status_id')->unsigned();
            $table->foreign('available_status_id')->references('id')->on('roomstatus');

            // $table->integer('role_id')->unsigned();
            // $table->foreign('role_id')->references('id')->on('roles');

            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblroomgroupstatus');
    }
}
