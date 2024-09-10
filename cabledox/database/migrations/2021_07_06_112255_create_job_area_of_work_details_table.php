<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobAreaOfWorkDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_area_of_work_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('job_cable_id');
            $table->foreign('job_cable_id')->references('id')->on('job_cables')->onDelete('cascade');
            $table->tinyInteger('installed')->comment('0=no, 1=yes')->default(0);
            $table->tinyInteger('checklist')->comment('0=no, 1=yes')->default(0);
            $table->tinyInteger('test_result')->comment('0=no, 1=yes')->default(0);


            $table->unsignedInteger('installed_by')->nullable();
            $table->unsignedInteger('checklist_by')->nullable();
            $table->unsignedInteger('test_result_by')->nullable();
            /*$table->timestamps();*/
            $table->timestamp('installed_at')->useCurrent()->nullable();
            $table->timestamp('checklist_at')->useCurrent()->nullable();
            $table->timestamp('test_result_at')->useCurrent()->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_area_of_work_details');
    }
}
