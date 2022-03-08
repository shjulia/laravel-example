<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddFromHourFieldToProviderAvailabilitiesTable
 */
class AddFromHourFieldToProviderAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->string('from_hour', 5)->nullable();
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
            $table->dropColumn('from_hour');
        });
    }
}
