<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteMaintenanceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_maintenance_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10)->nullable();
            $table->string('name', 100)->nullable();
            $table->integer('parent')->unsigned();
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('site_maintenance', function (Blueprint $table) {
            $table->integer('code')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_maintenance_categories');

       Schema::table('site_maintenance', function($table) {
            $table->dropColumn('code');
        });
    }
}
