<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('client creator id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('company_name');
            $table->string('company_email'); // ->unique()
            $table->string('company_phone')->nullable(); // ->unique()
            $table->string('company_logo')->nullable();
            $table->integer('no_of_jobs_allocated')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
