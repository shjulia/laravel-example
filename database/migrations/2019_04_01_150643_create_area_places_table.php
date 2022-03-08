<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_places', function (Blueprint $table) {
            $table->unsignedInteger('area_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('zip_id')->nullable();

            $table->primary(['area_id', 'city_id', 'zip_id']);
        });

        Schema::table('area_places', function (Blueprint $table) {
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('zip_id')->references('id')->on('zip_codes')->onDelete('cascade');
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
            $table->dropForeign(['area_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['zip_id']);
        });
        Schema::dropIfExists('area_places');
    }
}
