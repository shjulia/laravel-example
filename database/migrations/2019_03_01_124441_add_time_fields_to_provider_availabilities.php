<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddTimeFieldsToProviderAvailabilities
 */
class AddTimeFieldsToProviderAvailabilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->string('to_hour_max', 5);
            $table->string('to_hour_min', 5);
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
            $table->dropColumn('to_hour_max');
            $table->dropColumn('to_hour_min');
        });
    }
}
