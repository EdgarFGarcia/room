<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Settings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('local_id', 50)->nullable();
            $table->string('local_code', 50)->nullable();
            $table->integer('reservation_locked_out_time')->nullable();
            $table->integer('reservation_allowance')->nullable();
            $table->integer('automatic_general_cleaning_days')->nullable();
            $table->integer('automatic_general_cleaning_checkout')->nullable();
            $table->integer('automatic_preventive_maintenance_days')->nullable();
            $table->integer('automatic_preventive_maintance_checkout')->nullable();
            $table->integer('automated_clean_reclean')->nullable();
            $table->integer('automated_dirty_reclean')->nullable();
            $table->integer('staying_guest_12')->nullable();
            $table->integer('staying_guest_24')->nullable();
            $table->text('window_time_general_cleaning_start')->nullable();
            $table->text('window_time_general_cleaning_end')->nullable();
            $table->text('automatic_belo_percentage')->nullable();
            $table->text('automatic_belo_checkout')->nullable();
            $table->text('recovery_time')->nullable();
            $table->integer('user_restricted')->default(0);
            $table->integer('access_restricted')->default(0);
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
        Schema::dropIfExists('settings');
    }
}
