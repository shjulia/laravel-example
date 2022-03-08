<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeDistanceTable
 */
class ChangeDistanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distances', function (Blueprint $table) {
            $table->unsignedInteger('address_id')->nullable();
            $table->dropColumn('text');
            $table->string('distance_text');
            $table->float('duration');
            $table->string('duration_text');

            $table->foreign('address_id')->references('id')->on('practice_addresses')->onDelete('CASCADE');
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
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
            $table->string('text');
            $table->dropColumn('distance_text');
            $table->dropColumn('duration');
            $table->dropColumn('duration_text');
        });
    }
}
