<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurgePricesFieldsToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->float('surge_price')->default(0);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->float('surge_price')->default(0);
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
            $table->dropColumn('surge_price');
        });
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('surge_price');
        });
    }
}
