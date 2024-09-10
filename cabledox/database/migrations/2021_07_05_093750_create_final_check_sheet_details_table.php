<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalCheckSheetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_check_sheet_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('final_check_sheet_id');
            $table->foreign('final_check_sheet_id')->references('id')->on('final_check_sheets')->onDelete('cascade');
            $table->unsignedInteger('fcs_questionnaire_id')->comment('final_check_sheet_questionnaire_id');
            $table->foreign('fcs_questionnaire_id', 'questionnaire_id_foreign')->references('id')->on('final_check_sheet_questionnaires')->onDelete('cascade');

            $table->string('completed')->comment('Y, N, N/A');
            $table->text('comment')->nullable();

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
        Schema::dropIfExists('final_check_sheet_details');
    }
}
