<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblGrpRoomType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_grp_room_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_name');
            $table->integer('room_type_id')->unsigned();
            $table->foreign('room_type_id')->references('id')->on('tblroomtype');
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
        Schema::dropIfExists('tbl_grp_room_type');
    }
}
