<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCouponsTable
 */
class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->date('start_date');
            $table->date('end_date');
            $table->double('dollar_off')->nullable();
            $table->double('percent_off')->nullable();
            $table->double('minimum_bill')->nullable();
            $table->integer('use_per_account_limit')->nullable();
            $table->integer('use_globally_limit')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_state', function(Blueprint $table) {
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('state_id');

            $table->primary(['coupon_id', 'state_id']);
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('CASCADE');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('CASCADE');
        });

        Schema::create('coupon_position', function(Blueprint $table) {
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('position_id');

            $table->primary(['coupon_id', 'position_id']);
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('CASCADE');
            $table->foreign('position_id')->references('id')->on('states')->onDelete('CASCADE');
        });

        Schema::table('shifts', function(Blueprint $table) {
            $table->unsignedInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
        Schema::table('coupon_state', function(Blueprint $table) {
            $table->dropForeign('coupon_id');
            $table->dropForeign('state_id');
        });
        Schema::dropIfExists('coupon_state');
        Schema::table('coupon_position', function(Blueprint $table) {
            $table->dropForeign('coupon_id');
            $table->dropForeign('position_id');
        });
        Schema::dropIfExists('coupon_position');

        Schema::table('shifts', function(Blueprint $table) {
            $table->dropForeign('coupon_id');
        });
    }
}
