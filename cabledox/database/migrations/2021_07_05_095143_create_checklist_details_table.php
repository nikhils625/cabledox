<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('job_id');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->unsignedInteger('job_cable_id');
            $table->foreign('job_cable_id')->references('id')->on('cables')->onDelete('cascade');

            $table->unsignedInteger('checklist_master_id');
            $table->foreign('checklist_master_id')->references('id')->on('checklist_masters')->onDelete('cascade');

            /*$table->tinyInteger('checklist_type')->comment('0=cable_tied, 1=glanded, 2=cable_id_marker, 3=core_id_and_terminated, 4=inner_sheath_and_core sleaved, 5=earth_bonded');*/
            $table->string('name');
            $table->date('submit_date');

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
        Schema::dropIfExists('checklist_details');
    }
}
