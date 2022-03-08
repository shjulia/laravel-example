<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 2);
            $table->string('zip', 5);
            $table->string('place_name', 255);
            $table->string('state_name', 255);
            $table->string('state_code', 2);
            $table->string('county', 255);
            $table->string('county_code', 20);
            $table->string('community_name', 255)->nullable();
            $table->string('community_code', 20)->nullable();
            $table->decimal('lat');
            $table->decimal('lng');
            $table->string('accuracy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zip_codes');
    }
}
