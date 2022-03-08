<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAreaPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_places', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['zip_id']);
            $table->dropPrimary(['area_id', 'city_id', 'zip_id']);
            $table->dropIndex('area_places_city_id_foreign');
            $table->dropIndex('area_places_zip_id_foreign');
        });

        Schema::table('area_places', function (Blueprint $table) {
            $table->increments('id')->first();

            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('zip_id')->references('id')->on('zip_codes')->onDelete('cascade');
        });

        Schema::table('area_places', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->nullable()->change();
            $table->unsignedInteger('zip_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_places', function (Blueprint $table) {
            $table->unsignedInteger('city_id')->nullable(false)->change();
            $table->unsignedInteger('zip_id')->nullable(false)->change();

            $table->dropColumn('id');
            $table->primary(['area_id', 'city_id', 'zip_id']);
        });

    }
}
