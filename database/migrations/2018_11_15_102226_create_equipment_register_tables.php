<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentRegisterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Equipment
        //
        Schema::create('equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->tinyInteger('qty')->nullable();
            $table->text('notes')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        //
        // Equipment Location
        //
        Schema::create('equipment_location', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned()->default(0);
            $table->integer('site_id')->unsigned()->nullable();
            $table->string('other', 255)->nullable();
            $table->tinyInteger('qty')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('equipment')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        //
        // Equipment Transaction
        //
        Schema::create('equipment_trans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned()->default(0);
            $table->tinyInteger('qty')->nullable();
            $table->string('action', 5)->nullable();
            $table->text('notes')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('equipment')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_trans');
        Schema::dropIfExists('equipment_location');
        Schema::dropIfExists('equipment');

    }
}
