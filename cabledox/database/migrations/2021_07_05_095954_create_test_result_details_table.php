<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_result_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('job_id');

            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');

            $table->unsignedInteger('job_cable_id');

            $table->foreign('job_cable_id')->references('id')->on('job_cables')->onDelete('cascade');


            $table->unsignedInteger('test_parameter_id');

            $table->foreign('test_parameter_id')->references('id')->on('test_parameters')->onDelete('cascade');

            $table->string('output');
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
        Schema::dropIfExists('test_result_details');
    }
}
