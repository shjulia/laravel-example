<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewslattersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('html_content');
            $table->text('json_content');
            $table->timestamps();
        });

        Schema::create('newsletter_newsletters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->string('subject');
            $table->dateTime('start_date');
            $table->string('tz');
            $table->smallInteger('is_finished')->default(0);
            $table->text('emails');
            $table->unsignedInteger('role_id')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('newsletter_templates')->onDelete('CASCADE');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletter_newsletters', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropForeign(['role_id']);
        });
        Schema::dropIfExists('newsletter_newsletters');
        Schema::dropIfExists('newsletter_templates');
    }
}
