<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQaCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_qa_categories', function (Blueprint $table) {
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

        Schema::table('site_qa', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->nullable();
        });

        Schema::table('site_qa_items', function (Blueprint $table) {
            $table->tinyInteger('certification')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_qa_items', function($table) {
            $table->dropColumn('certification');
        });

        Schema::table('site_qa', function($table) {
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('site_qa_categories');
    }
}
