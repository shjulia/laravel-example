<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddPositionsToLicenseTypesTable
 */
class AddPositionsToLicenseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });

        Schema::create('license_types_positions', function (Blueprint $table) {
            $table->unsignedInteger('license_type_id');
            $table->unsignedInteger('position_id');

            $table->foreign('license_type_id')->references('id')->on('license_types')->onDelete('CASCADE');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('license_types_positions', function (Blueprint $table) {
            $table->dropForeign(['license_type_id']);
            $table->dropForeign(['position_id']);
        });
        Schema::dropIfExists('license_types_positions');

        Schema::table('license_types', function (Blueprint $table) {
            $table->unsignedInteger('position_id')->nullable();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('CASCADE');
        });
    }
}
