<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            Schema::create('cities', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->string('tier')->nullable();
                $table->unsignedInteger('county');
                $table->decimal('lat');
                $table->decimal('lng');
                $table->text('alternames')->nullable();
            });

            Schema::table('cities', function (Blueprint $table) {
                $table->foreign('county')->references('id')->on('counties');
            });
        });
    }

    public function down()
    {
        DB::transaction(function () {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropForeign(['county']);
            });
            Schema::dropIfExists('cities');
        });
    }
}
