<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdsSwmsTbtTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Safety Docs - SDS
        //
        Schema::create('safety_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10);
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('name', 100);
            $table->string('attachment', 255);
            $table->dateTime('expiry')->nullable();
            $table->string('reference', 50);
            $table->string('version', 10);
            $table->string('share', 2)->default('b');
            $table->text('notes');
            $table->tinyInteger('status')->default(1);
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->default(0);

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('for_company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('safety_docs_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10);
            $table->string('name', 100);
            $table->integer('parent')->unsigned();
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Toolbox Talks
        //
        Schema::create('toolbox_talks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->text('overview');
            $table->text('hazards');
            $table->text('controls');
            $table->text('further');
            $table->string('version', 10);
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();
            $table->integer('authorised_by')->unsigned()->nullable();
            $table->timestamp('authorised_at')->nullable();
            $table->timestamp('review_at')->nullable();
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('share', 2)->default('b');

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('for_company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // SWMS
        //
        Schema::create('wms_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('project', 100);
            $table->string('attachment', 255);
            $table->string('reference', 50);
            $table->string('version', 10);
            $table->tinyInteger('builder')->default(0);
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();
            $table->string('principle', 100)->nullable();
            $table->integer('principle_id')->unsigned()->nullable();
            $table->integer('principle_signed_id')->unsigned()->nullable();
            $table->timestamp('principle_signed_at')->nullable();
            $table->integer('user_signed_id')->unsigned()->nullable();
            $table->timestamp('user_signed_at')->nullable();
            $table->text('res_compliance');
            $table->text('res_review');
            $table->text('notes');
            $table->string('share', 2)->default('b');
            $table->tinyInteger('status')->default(0);
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('for_company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wms_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doc_id')->unsigned();
            $table->text('name');
            $table->integer('order')->unsigned();
            $table->string('likelihood', 25);
            $table->string('consequence', 25);
            $table->string('rating', 25);
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('doc_id')->references('id')->on('wms_docs')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wms_hazards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('step_id')->unsigned();
            $table->text('name');
            $table->integer('order')->unsigned();
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('step_id')->references('id')->on('wms_steps')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wms_controls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('step_id')->unsigned();
            $table->text('name');
            $table->tinyInteger('res_principle');
            $table->tinyInteger('res_company');
            $table->tinyInteger('res_worker');
            $table->integer('order')->unsigned();
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('step_id')->references('id')->on('wms_steps')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });


        //
        // Safety Tips
        //
        Schema::create('safety_tips', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 250);
            $table->text('body');
            $table->dateTime('last_published');
            $table->tinyInteger('status')->default(0);

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
        Schema::dropIfExists('safety_tips');
        Schema::dropIfExists('wms_controls');
        Schema::dropIfExists('wms_hazards');
        Schema::dropIfExists('wms_steps');
        Schema::dropIfExists('wms_docs');
        Schema::dropIfExists('toolbox_talks');
        Schema::dropIfExists('safety_docs_categories');
        Schema::dropIfExists('safety_docs');
    }
}
