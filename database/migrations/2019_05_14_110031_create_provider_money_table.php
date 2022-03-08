<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_money', function (Blueprint $table) {
            $table->unsignedInteger('provider_id');
            $table->primary('provider_id');
            $table->float('earns');
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
        Schema::dropIfExists('provider_money');
    }
}
