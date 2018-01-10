<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Company has a list of trades they use for planner
        Schema::create('trades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        // Each Trade has a list of tasks associated with it
        Schema::create('trade_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trade_id')->unsigned();
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            $table->string('name',50)->nullable();
            $table->string('code',15)->nullable();
            $table->tinyInteger('upcoming')->default(0);
            $table->tinyInteger('status')->default(1);

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        // Child Company is assigned multiple trades from Parent Company Trade List (ie Carpenter, Plumber)
        Schema::create('company_trade', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
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
        Schema::dropIfExists('company_trade');
        Schema::dropIfExists('trade_task');
        Schema::dropIfExists('trades');
    }
}
