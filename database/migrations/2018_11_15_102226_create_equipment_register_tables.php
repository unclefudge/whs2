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
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('name', 100)->nullable();
            $table->tinyInteger('purchased')->unsigned()->default(0);
            $table->tinyInteger('disposed')->unsigned()->default(0);
            $table->tinyInteger('status')->default(1);
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
        // Equipment Categpries
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10)->nullable();
            $table->string('name', 100)->nullable();
            $table->integer('parent')->unsigned();
            $table->tinyInteger('private')->default(0);
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys');

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
            $table->integer('site_id')->unsigned()->nullable();
            $table->string('other', 255)->nullable();
            $table->tinyInteger('status')->default(1);
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
        // Equipment Location Items
        //
        Schema::create('equipment_location_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('equipment_id')->unsigned()->nullable();
            $table->tinyInteger('qty')->nullable();
            $table->tinyInteger('extra')->default(0);

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('equipment_location')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });


        //
        // Equipment Stocktake
        //
        Schema::create('equipment_stocktake', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned()->nullable();
            $table->tinyInteger('passed')->defult(1);
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
        // Equipment Stocktake Items
        //
        Schema::create('equipment_stocktake_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stocktake_id')->unsigned()->nullable();
            $table->integer('equipment_id')->unsigned()->nullable();
            $table->integer('qty_expect')->nullable();
            $table->integer('qty_actual')->nullable();
            $table->tinyInteger('passed')->nullable()->defult(1);

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('stocktake_id')->references('id')->on('equipment_stocktake')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        //
        // Equipment Lost
        //
        Schema::create('equipment_lost', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('equipment_id')->unsigned()->nullable();
            $table->tinyInteger('qty')->nullable();
            $table->text('notes')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('equipment_location')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        //
        // Equipment Log
        //
        Schema::create('equipment_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('equipment_id')->unsigned()->default(0);
            $table->tinyInteger('qty')->nullable();
            $table->string('action', 5)->nullable();
            $table->text('notes')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

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
        Schema::dropIfExists('equipment_log');
        Schema::dropIfExists('equipment_lost');
        Schema::dropIfExists('equipment_stocktake_items');
        Schema::dropIfExists('equipment_stocktake');
        Schema::dropIfExists('equipment_location_items');
        Schema::dropIfExists('equipment_location');
        Schema::dropIfExists('equipment_categories');
        Schema::dropIfExists('equipment');

    }
}
