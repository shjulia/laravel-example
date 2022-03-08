<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProviderChargesTable
 */
class CreateProviderChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shift_id');
            $table->unsignedInteger('provider_id');
            $table->string('charge_id')->nullable();
            $table->string('payment_system')->nullable();
            $table->float('amount');
            $table->string('status')->default('in boon');
            $table->float('commission')->nullable();
            $table->boolean('is_main')->default(1);
            $table->float('decreased_amount')->default(0);
            $table->float('debt_covered_amount')->default(0);
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('provider_id')->references('user_id')->on('specialists')->onDelete('cascade');
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->float('debt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn('debt');
        });
        Schema::table('provider_charges', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['provider_id']);
        });
        Schema::dropIfExists('provider_charges');
    }
}
