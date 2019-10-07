<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VcAuditTrail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vc_audit_trail', function (Blueprint $table) {
            $table->increments('VCAT_ID');
            $table->dateTime('VCAT_Date');
            $table->integer('VCAT_Status_Prev');
            $table->integer('VCAT_Status_From');
            $table->integer('VCAT_Status_To');
            $table->string('VCAT_Emp', '255');
            $table->integer('VCAT_Emp_Position');
            $table->integer('VCAT_Room');
            $table->integer('VCAT_Locale');
            $table->integer('VCAT_Inspected');
            $table->text('Vc_Notes');
            $table->integer('changed_from');
            $table->bigInteger('flag');
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
        Schema::dropIfExists('vc_audit_trail');
    }
}
