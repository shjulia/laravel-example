<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToCityTable extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            Schema::table('cities', function (Blueprint $table) {
                $table->unsignedInteger('state')->nullable();
            });

            Schema::table('cities', function (Blueprint $table) {
                $table->foreign('state')->references('id')->on('states')->onDelete('cascade');
            });
        });
    }

    public function down()
    {
        DB::transaction(function () {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropForeign(['state']);
            });

            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn(['state']);
            });
        });
    }
}
