<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RmsLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rms_logs', function (Blueprint $table) {
            $table->integer('id');

            $table->integer('customer_id')->nullale();

            $table->integer('room_id');
            $table->dateTime('p_datetime');
            $table->integer('p_status');

            $table->integer('s_status');
            $table->dateTime('s_dateTime'); 
            // $table->integer('s_emp_id');
            
            $table->integer('e_status')->nullale();
            // $table->integer('e_emp_id')->nullale();
            $table->dateTime('e_dateTime')->nullale();
            // $table->string('trans_from'); //kung mobile o web

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
        Schema::dropIfExists('rms_logs');
    }
}
