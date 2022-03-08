<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateRatesTable
 */
class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('rate_position', function (Blueprint $table) {
            $table->unsignedInteger('rate_id');
            $table->unsignedInteger('position_id');
            $table->float('rate')->nullable();;
            $table->float('minimum_profit');
            $table->float('surge_price')->default(0);
            $table->float('max_day_rate')->nullable();

            $table->primary(['rate_id', 'position_id']);
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('CASCADE');
            $table->foreign('rate_id')->references('id')->on('rates')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_position', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['rate_id']);
        });
        Schema::dropIfExists('rate_position');
        Schema::dropIfExists('rates');
    }
}
