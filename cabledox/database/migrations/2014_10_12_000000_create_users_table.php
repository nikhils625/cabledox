<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email'); // ->unique()
            $table->string('phone')->nullable(); // ->unique()
            $table->string('user_profile')->nullable();
            $table->integer('role_id');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->tinyInteger('status')->comment('0=inactive, 1=active');
            $table->integer('created_by');
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
        Schema::dropIfExists('users');
    }
}
