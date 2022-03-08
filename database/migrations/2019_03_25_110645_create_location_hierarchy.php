<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationHierarchy extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('geo')) {
            Schema::create('geo', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable();
                $table->integer('left')->nullable();
                $table->integer('right')->nullable();
                $table->integer('depth')->default(0);
                $table->char('name', 60);
                $table->json('alternames');
                $table->char('country', 2);
                $table->char('level', 10);
                $table->bigInteger('population');
                $table->decimal('lat',9,6);
                $table->decimal('long',9,6);
            });
        }
    }

    public function down()
    {
        DB::transaction(function () {
            Schema::dropIfExists('geo');
        });
    }
}
