<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProviderBonusesTable
 */
class CreateProviderBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('provider_id');
            $table->float('bonus_value');
            $table->float('bonus_h');
            $table->string('status');
            $table->string('charge_id')->nullable();
            $table->string('payment_system')->nullable();
            $table->timestamps();

            $table->foreign('provider_id')->references('user_id')->on('specialists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_bonuses', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
        });
        Schema::dropIfExists('provider_bonuses');
    }
}
