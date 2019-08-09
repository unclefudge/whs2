<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentLocationOtherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_location_other', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10)->nullable();
            $table->string('name', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys');

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
        Schema::dropIfExists('equipment_location_other');
    }
}
