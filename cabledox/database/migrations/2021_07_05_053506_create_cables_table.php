<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cables', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedInteger('job_id');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');

            $table->unsignedInteger('cable_type_id')->comment('cable types id');
            $table->foreign('cable_type_id')->references('id')->on('cable_types')->onDelete('cascade');

            $table->unsignedInteger('cable_id_type')->comment('cable_master_id');
            $table->foreign('cable_id_type')->references('id')->on('cable_masters')->onDelete('cascade');

            $table->string('cable_id')->nullable();
            $table->string('custom_id')->nullable();
            $table->string('unique_code')->nullable();
            /*$table->string('to');
            $table->string('from');*/
            $table->double('size', 10, 2);
            $table->text('description')->nullable();
            $table->text('additional_information')->nullable();

            $table->string('file_name')->nullable();
            $table->tinyInteger('status')->comment('0=inactive, 1=active');

            /*$table->timestamps();*/
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
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
        Schema::dropIfExists('cables');
    }
}
