<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkrs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('specialist_id');
            $table->string('checkr_status', 50)->default('not checked');
            $table->string('checkr_candidate_id', 255)->nullable();
            $table->string('checkr_report_id', 255)->nullable();
            $table->text('checkr_error_response')->nullable();
            $table->text('checkr_success_response')->nullable();
            $table->integer('checkr_attempts')->default(0);

            $table->foreign('specialist_id')
                ->references('user_id')
                ->on('specialists')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkrs');
    }
}
