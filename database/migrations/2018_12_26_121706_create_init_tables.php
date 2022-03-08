<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateInitTables
 */
class CreateInitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->integer('industry_id')->unsigned();

            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('CASCADE');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 50)->nullable();
            $table->string('tmp_token', 50)->nullable();
            $table->string('status', 20);
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
        });
        Schema::dropIfExists('positions');

        Schema::dropIfExists('industries');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('phone');
            $table->dropColumn('tmp_token');
            $table->dropColumn('status');
            $table->string('name', 50);
        });
    }
}
