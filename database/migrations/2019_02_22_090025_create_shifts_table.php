<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShiftsTable
 */
class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('practice_id');
            $table->string('status', 20);
            $table->unsignedInteger('position_id')->nullable();
            $table->date('date')->nullable();
            $table->string('from_time', 5)->nullable();
            $table->string('to_time', 5)->nullable();
            $table->float('shift_time')->nullable();
            $table->text('tasks')->nullable();
            $table->float('cost')->nullable();
            $table->float('arrival_time')->nullable();
            $table->unsignedInteger('provider_id')->nullable();
            $table->timestamps();

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
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['practice_id']);
        });
        Schema::dropIfExists('shifts');
    }
}
