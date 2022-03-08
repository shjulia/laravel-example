<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLatLngPrecisionInCitiesAndZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->float('lat', 15, 7);
            $table->float('lng', 15, 7);
        });

        Schema::table('zip_codes', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        Schema::table('zip_codes', function (Blueprint $table) {
            $table->float('lat', 15, 7);
            $table->float('lng', 15, 7);
        });

        Schema::table('counties', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        Schema::table('counties', function (Blueprint $table) {
            $table->float('lat', 15, 7);
            $table->float('lng', 15, 7);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->float('lat', 8, 2);
            $table->float('lng', 8, 2);
        });

        Schema::table('zip_codes', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
        Schema::table('zip_codes', function (Blueprint $table) {
            $table->float('lat', 8, 2);
            $table->float('lng', 8, 2);
        });

        Schema::table('counties', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
        Schema::table('counties', function (Blueprint $table) {
            $table->float('lat', 8, 2);
            $table->float('lng', 8, 2);
        });
    }
}
