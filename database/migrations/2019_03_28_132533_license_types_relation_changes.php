<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class LicenseTypesRelationChanges
 */
class LicenseTypesRelationChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->dropColumn('state');
        });
        Schema::table('license_types_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('required');
        });

        Schema::create('license_types_positions_states', function (Blueprint $table) {
            $table->unsignedInteger('license_types_position_id');
            $table->unsignedInteger('state_id');

            $table->primary(['license_types_position_id', 'state_id'], 'ltpistpr');
            $table->foreign('license_types_position_id')->references('id')->on('license_types_positions')->onDelete('CASCADE');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->string('state', 3)->nullable();
        });

        Schema::table('license_types_positions_states', function (Blueprint $table) {
            $table->dropForeign(['license_types_position_id']);
            $table->dropForeign(['state_id']);
        });
        Schema::dropIfExists('license_types_positions_states');

        Schema::table('license_types_positions', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('required');
        });
    }
}
