<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShiftLogsTable
 */
class CreateShiftLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shift_id');
            $table->text('action');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->onDelete('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('shift_logs');
    }
}
