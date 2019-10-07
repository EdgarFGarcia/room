<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Roomstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roomstatus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('room_status');
            $table->string('color');
            $table->integer('is_blink')->default(0);
            $table->integer('is_timer')->default(0);
            $table->integer('is_name')->default(0);
            $table->integer('is_buddy')->default(0);
            $table->integer('is_cancel')->default(0);
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
        Schema::dropIfExists('roomstatus');
    }
}
