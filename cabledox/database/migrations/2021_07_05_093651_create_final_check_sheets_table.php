<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalCheckSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_check_sheets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedInteger('job_id');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');

            $table->unsignedInteger('cable_location_id')->comment('area_inspected');
            $table->foreign('cable_location_id')->references('id')->on('job_locations')->onDelete('cascade');

            $table->unsignedInteger('cable_id');
            $table->foreign('cable_id')->references('id')->on('cables')->onDelete('cascade');

            $table->string('upload_image')->nullable();

            $table->string('inspector_name');
            $table->string('inspector_signature');
            $table->date('inspector_signature_date');

            $table->string('pc_inspector_name')->comment('principle_contractor_inspector_name');
            $table->string('pc_inspector_signature')->comment('principle_contractor_inspector_signature');
            $table->date('pc_inspector_signature_date')->comment('principle_contractor_inspector_signature_date');

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
        Schema::dropIfExists('final_check_sheets');
    }
}
