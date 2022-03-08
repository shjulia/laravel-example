<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePracticeAddressesTable
 */
class CreatePracticeAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practice_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('practice_id');
            $table->string('practice_name', 255)->nullable();
            $table->string('address', 255);
            $table->string('city', 255);
            $table->string('state', 20);
            $table->string('zip', 10);
            $table->string('url', 255)->nullable();
            $table->string('practice_phone', 100)->nullable();

            $table->foreign('practice_id')
                ->references('id')
                ->on('practices')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('practice_addresses', function (Blueprint $table) {
            $table->dropForeign('practice_id');
        });
        Schema::dropIfExists('practice_addresses');
    }
}
