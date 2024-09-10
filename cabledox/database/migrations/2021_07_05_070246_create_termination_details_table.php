<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termination_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('termination_id');
            $table->foreign('termination_id')->references('id')->on('terminations')->onDelete('cascade');
            $table->unsignedInteger('cable_master_detail_id');
            $table->foreign('cable_master_detail_id')->references('id')->on('cable_master_details')->onDelete('cascade');

            $table->string('core_id');
            $table->string('termination_location');

            /*$table->timestamps();*/
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termination_details');
    }
}
