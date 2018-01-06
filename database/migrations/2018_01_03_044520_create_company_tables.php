<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Company
        //
        Schema::create('companys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('nickname', 100);
            $table->string('slug', 100)->unique();
            $table->string('email');
            $table->string('phone', 50);
            $table->string('logo_banner', 250);
            $table->string('logo_profile', 250);

            // Address
            $table->string('address', 200);
            $table->string('address2', 200);
            $table->string('suburb', 150);
            $table->string('state', 40);
            $table->string('postcode', 10);
            $table->string('country', 100);

            // Business details
            $table->string('abn', 20);
            $table->tinyInteger('gst')->nullable();
            $table->tinyInteger('payroll_tax')->nullable();
            $table->string('creditor_code', 25);
            $table->string('business_entity', 25);
            $table->string('sub_group', 25);
            $table->string('category', 50);

            // Trade details
            $table->tinyInteger('licence_required')->default(1);
            $table->tinyInteger('maxjobs');
            $table->tinyInteger('transient')->default(0);

            // Contacts
            $table->integer('primary_user')->unsigned()->default(0);
            $table->integer('secondary_user')->unsigned()->default(0);

            // Additional fields
            $table->text('notes');
            $table->tinyInteger('subscription')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->integer('parent_company')->unsigned()->default(0);

            // Modify info
            $table->integer('approved_by')->unsigned();
            $table->dateTime('approved_at')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // temporary
            $table->string('licence_no', 25);
            $table->string('licence_type', 25);
            $table->dateTime('licence_expiry');
            $table->tinyInteger('entity')->default(0);
        });

        //
        // Each Company has a list of Supervisors who may also have a Seniors Supervisor of them
        //
        Schema::create('company_supervisors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('parent_id')->unsigned();

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
        // Each Child Company may be assigned multiple supervisors from Parent Company
        //     - used only for Transient companies
        Schema::create('company_supervisors_transient', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Company leave
        //
        Schema::create('company_leave', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->dateTime('from')->index();
            $table->dateTime('to')->index();
            $table->text('notes');

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Company Docs
        //
        Schema::create('company_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10);
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('name', 100);
            $table->string('attachment', 255);
            $table->dateTime('expiry')->nullable();
            $table->string('version', 10);
            $table->string('ref_no', 50);
            $table->string('ref_name', 50);
            $table->string('ref_type', 100);
            $table->tinyInteger('private')->default(0);
            $table->string('share', 2)->default('b');
            $table->text('notes');
            $table->tinyInteger('status')->default(1);
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->default(0);
            $table->integer('approved_by')->unsigned();
            $table->dateTime('approved_at')->nullable();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys');
            $table->foreign('for_company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('company_docs_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10);
            $table->string('name', 100);
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
            $table->softDeletes();
        });

        Schema::create('contractor_licence', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 25);
            $table->string('name', 250);
            $table->integer('parent')->unsigned()->default(0);
            $table->tinyInteger('status')->default(1);
            $table->text('notes');

            // Modify info
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
        Schema::dropIfExists('contractor_licence');
        Schema::dropIfExists('company_docs_categories');
        Schema::dropIfExists('company_docs');
        Schema::dropIfExists('company_leave');
        Schema::dropIfExists('company_supervisors_transient');
        Schema::dropIfExists('company_supervisors');
        Schema::dropIfExists('companys');
    }
}
