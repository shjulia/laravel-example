<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeDistributorTable
 */
class ChangeDistributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Delete old data */
        DB::table('roles')->where('type', 'distributor')->delete();

        Schema::table('invites', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('invites');

        Schema::table('distributors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('distributors');

        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('partners');

        /* Create new */
        DB::table('roles')->insert([
            'title' => 'Partner',
            'type' => 'partner'
        ]);
        Schema::create('partners', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('description', 255);
            $table->string('description_answer', 255);

            $table->primary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('referral_code', 255);
            $table->integer('referred_amount');
            $table->float('referral_money_earned');

            $table->primary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });

        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('referral_id');
            $table->string('email', 255);
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('accepted')->default(0);
            $table->timestamps();

            $table->foreign('referral_id')->references('user_id')->on('referrals')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->where('type', 'partners')->delete();

        Schema::table('invites', function (Blueprint $table) {
            $table->dropForeign(['referral_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('invites');

        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('partners');

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('referrals');

        DB::table('roles')->insert([
            'title' => 'Distributor',
            'type' => 'distributor'
        ]);
        Schema::create('distributors', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('description', 255);
            $table->string('description_answer', 255);

            $table->primary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('referral_code', 255);
            $table->integer('referred_amount');
            $table->float('referral_money_earned');

            $table->primary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });

        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('partner_id');
            $table->string('email', 255);
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('accepted')->default(0);
            $table->timestamps();

            $table->foreign('partner_id')->references('user_id')->on('partners')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }
}
