<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveExtraFieldsFromCitiesAndZipCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign('cities_county_foreign');
            $table->dropColumn(['county', 'alternames']);
        });

        Schema::table('zip_codes', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'state_name', 'county_code', 'community_name', 'community_code', 'accuracy']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->string('country_code', 2);
            $table->string('state_name', 255);
            $table->string('county_code', 20);
            $table->string('community_name', 255)->nullable();
            $table->string('community_code', 20)->nullable();
            $table->string('accuracy')->nullable();
        });
        Schema::create('cities', function (Blueprint $table) {
            $table->unsignedInteger('county');
            $table->text('alternames')->nullable();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('county')->references('id')->on('counties');
        });
    }
}
