<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddGeocodesFieldsToUsersTables
 */
class AddGeocodesFieldsToUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
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
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
}
