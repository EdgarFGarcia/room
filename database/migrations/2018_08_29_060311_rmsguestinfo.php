<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Rmsguestinfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rmsguestinfo', function (Blueprint $table) {
            $table->bigInteger('id');

            $table->string('RoomName');
            $table->string('RoomNo');
            $table->string('RoomType');
            $table->string('DateIn');
            $table->string('TimeIn');
            $table->double('RateAmount', 30, 2);
            $table->string('RateDesc');
            $table->string('MarketSource');
            $table->string('CarMake');
            $table->string('VehicleType');
            $table->string('PlateNo');
            $table->string('GSteward');
            $table->text('Remarks');
            $table->bigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('user');

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
        Schema::dropIfExists('rmsguestinfo');
    }
}
