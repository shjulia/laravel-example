<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateEmailLogsTable
 */
class CreateEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('to');
            $table->string('class', 255);
            $table->string('subject', 255);
            $table->text('data');
            $table->string('last_status', 255)->nullable();
            $table->string('message_id', 255)->nullable();
            $table->timestamps();

            $table->foreign('to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropForeign(['to']);
        });
        Schema::dropIfExists('email_logs');
    }
}
