<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLicensesTable
 */
class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('specialist_id');
            $table->unsignedInteger('parent_license_id')->nullable();
            $table->smallInteger('is_main')->default(0);
            $table->string('photo', 255)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('number', 255)->nullable();
            $table->timestamp('expiration_date')->nullable();
            $table->timestamps();

            $table->foreign('specialist_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('parent_license_id')->references('id')->on('licenses')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['specialist_id']);
            $table->dropForeign(['parent_license_id']);
        });
        Schema::dropIfExists('licenses');
    }
}
