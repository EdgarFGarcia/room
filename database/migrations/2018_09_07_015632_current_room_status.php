<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CurrentRoomStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_room_status', function (Blueprint $table) {
            $table->integer('CRS_Room_ID');
            $table->string('CRS_Room_Status', '255');
            $table->integer('CRS_Room_GC_Count');
            $table->date('CRS_Room_Last_GC');
            $table->integer('CRS_Emp_Last');
            $table->dateTime('CRS_Last_Update_CO');
            $table->dateTime('CRS_Last_Updated');
            $table->integer('CRS_Emp_Last_Updated');
            $table->dateTime('CRS_Last_Clean');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_room_status');
    }
}
