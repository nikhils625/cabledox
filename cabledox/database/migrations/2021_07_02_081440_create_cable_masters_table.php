<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCableMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cable_masters', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            /*$table->unsignedInteger('cable_type_id');
            $table->foreign('cable_type_id')->references('id')->on('cable_types')->onDelete('cascade');*/

            $table->string('cable_type_id');

            $table->integer('cores')->default(1);
            /*$table->tinyInteger('cable_is_pair_triple_quad')->default(0)->comment('0 = empty, 2 = pairs, 3 = triple, 4 = quad');*/
            $table->integer('no_of_pair_triple_quad')->default(1);

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
        Schema::dropIfExists('cable_masters');
    }
}
