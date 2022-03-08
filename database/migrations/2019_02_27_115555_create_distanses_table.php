<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistansesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('practice_id');
            $table->unsignedInteger('provider_id');
            $table->float('distance');

            $table->foreign('provider_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distances', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['practice_id']);
        });
        Schema::dropIfExists('distances');
    }
}
