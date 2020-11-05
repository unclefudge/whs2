<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteInspectionPlumbingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_inspection_plumbing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->nullable();

            // Client
            $table->string('client_name', 150)->nullable();
            $table->string('client_address', 255)->nullable();
            $table->tinyInteger('client_contacted')->nullable();

            // Inspection
            $table->integer('assigned_to')->unsigned()->nullable();
            $table->integer('inspected_by')->unsigned()->nullable();
            $table->dateTime('inspected_at')->nullable();
            $table->string('inspected_name', 100)->nullable();
            $table->string('inspected_lic', 20)->nullable();

            // Inspection details
            $table->string('pressure', 15)->nullable();
            $table->tinyInteger('pressure_reduction')->nullable();
            $table->string('pressure_cost', 15)->nullable();
            $table->text('pressure_notes')->nullable();
            $table->string('hammer', 10)->nullable();
            $table->text('hammer_notes')->nullable();
            $table->string('hotwater_type', 25)->nullable();
            $table->tinyInteger('hotwater_lowered')->nullable();
            $table->string('fuel_type', 25)->nullable();
            $table->string('gas_position', 10)->nullable();
            $table->string('gas_pipes', 25)->nullable();
            $table->tinyInteger('gas_lines')->nullable();
            $table->text('gas')->nullable();
            $table->text('existing')->nullable();
            $table->text('existing_notes')->nullable();
            $table->string('sewer_cost', 10)->nullable();
            $table->string('sewer_allowance', 10)->nullable();
            $table->string('sewer_extra', 10)->nullable();
            $table->text('sewer_notes')->nullable();
            $table->string('stormwater_cost', 10)->nullable();
            $table->string('stormwater_allowance', 10)->nullable();
            $table->string('stormwater_extra', 10)->nullable();
            $table->text('stormwater_notes')->nullable();
            $table->string('stormwater_detention_type', 10)->nullable();
            $table->text('stormwater_detention_notes')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

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
        Schema::dropIfExists('site_inspection_plumbing');
    }
}
