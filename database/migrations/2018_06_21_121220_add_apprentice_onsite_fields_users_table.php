<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprenticeOnsiteFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('onsite')->default(1);
            $table->tinyInteger('contractor_licence')->default(0);
            $table->tinyInteger('supervisor_licence')->default(0);
            $table->tinyInteger('apprentice')->default(0);
            $table->dateTime('apprentice_start')->nullable();
            $table->integer('approved_by')->unsigned();
            $table->dateTime('approved_at')->nullable();
        });

        // User may have multiple trades (ie Carpenter, Plumber)
        Schema::create('user_trade', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('trade_id')->unsigned()->index();
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_trade');
        Schema::table('users', function($table) {
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
            $table->dropColumn('apprentice_start');
            $table->dropColumn('apprentice');
            $table->dropColumn('supervisor_licence');
            $table->dropColumn('contractor_licence');
            $table->dropColumn('onsite');
        });
    }
}
