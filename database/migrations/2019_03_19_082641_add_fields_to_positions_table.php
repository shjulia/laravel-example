<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddFieldsToPositionsTable
 */
class AddFieldsToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->float('fee');
            $table->float('minimum_profit');
        });
        Schema::table('shifts', function (Blueprint $table) {
            $table->float('cost_for_practice')->nullable();
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
            $table->dropColumn('fee');
            $table->dropColumn('minimum_profit');
        });
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('cost_for_practice');
        });
    }
}
