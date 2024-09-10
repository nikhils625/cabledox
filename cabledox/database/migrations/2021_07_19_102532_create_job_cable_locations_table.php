<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCableLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_cable_locations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('job_cable_id');
            $table->foreign('job_cable_id')->references('id')->on('job_cables')->onDelete('cascade');

            $table->unsignedInteger('location_id');
            $table->foreign('location_id')->references('id')->on('job_locations')->onDelete('cascade');

            $table->tinyInteger('location_type')->comment('0=to, 1=from');

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
        Schema::dropIfExists('job_cable_locations');
    }
}
