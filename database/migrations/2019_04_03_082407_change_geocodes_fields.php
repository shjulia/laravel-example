<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeGeocodesFields
 */
class ChangeGeocodesFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
        Schema::table('practices', function (Blueprint $table) {
            $table->float('lat', 15, 7)->nullable();
            $table->float('lng', 15, 7)->nullable();
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
        Schema::table('specialists', function (Blueprint $table) {
            $table->float('lat', 15, 7)->nullable();
            $table->float('lng', 15, 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
