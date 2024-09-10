<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCableMasterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cable_master_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('cable_master_id');
            $table->foreign('cable_master_id')->references('id')->on('cable_masters')->onDelete('cascade');
            $table->string('core_name');
            $table->tinyInteger('core_index');
            $table->tinyInteger('wire_index');

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
        Schema::dropIfExists('cable_master_details');
    }
}
