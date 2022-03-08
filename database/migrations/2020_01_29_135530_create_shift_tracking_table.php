<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shift_id');
            $table->string('action');
            $table->float('lat', 15, 7)->nullable();
            $table->float('lng', 15, 7)->nullable();
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_tracking', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
        });
        Schema::dropIfExists('shift_tracking');
    }
}
