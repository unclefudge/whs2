<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteInspectionDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_inspection_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inspect_id')->unsigned()->nullable();
            $table->string('table', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('attachment', 255)->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1);

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
        Schema::dropIfExists('site_inspection_docs');
    }
}
