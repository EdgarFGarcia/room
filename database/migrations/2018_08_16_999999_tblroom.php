<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class Tblroom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblroom', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('room_no');
            $table->text('room_name')->nullable();
            $table->longtext('room_description')->nullable();
            $table->integer('room_area_id')->unsigned();
            $table->foreign('room_area_id')->references('id')->on('tblroomareas');

            $table->string('room_no');
            $table->text('room_name');

            $table->integer('room_status_id')->unsigned();
            $table->foreign('room_status_id')->references('id')->on('roomstatus');

            $table->integer('from_room_status_id');

            $table->integer('room_type_id')->unsigned();
            $table->foreign('room_type_id')->references('id')->on('tblroomtype');

            $table->timestamp('last_check_in');
            $table->integer('checkout_count')->default(0);
            $table->integer('preventive_checkout_count')->default(0);
            $table->integer('belo_count')->default(0);

            $table->string('room_hashed')->nullable();

            $table->timestamp('last_general_cleaning')->default(DB::raw("NOW()"));
            $table->integer('is_pest')->default(0);
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
        Schema::dropIfExists('tblroom');
    }
}
