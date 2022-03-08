<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateToolsTable
 */
class CreateToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('is_regular')->default(true);
            $table->timestamps();
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->unsignedInteger('tool_id')->nullable();

            $table->foreign('tool_id')->references('id')->on('tools')->onDelete('SET NULL');
        });
        Schema::table('specialists', function (Blueprint $table) {
            $table->unsignedInteger('tool_id')->nullable();

            $table->foreign('tool_id')->references('id')->on('tools')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign(['tool_id']);
            $table->dropColumn('tool_id');
        });
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropForeign(['tool_id']);
            $table->dropColumn('tool_id');
        });
        Schema::dropIfExists('tools');
    }
}
