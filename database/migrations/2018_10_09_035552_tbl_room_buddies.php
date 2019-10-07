<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblRoomBuddies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_room_buddies', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('rms_logs_id');
            $table->integer('emp_id');
            $table->integer('room_no');
            $table->string('from');
            $table->integer('work_id');

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
        Schema::dropIfExists('tbl_room_buddies');
    }
}
