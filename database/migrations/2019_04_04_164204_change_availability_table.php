<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeAvailabilityTable
 */
class ChangeAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->float('min_time')->nullable()->change();
            $table->float('max_time')->nullable()->change();
            $table->string('to_hour_max', 5)->nullable()->change();
            $table->string('to_hour_min', 5)->nullable()->change();
            $table->string('to_hour', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->dropColumn('to_hour');

        });
    }
}
