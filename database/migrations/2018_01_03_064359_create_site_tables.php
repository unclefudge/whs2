<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('code')->unique();
            $table->string('slug')->unique();

            // Address
            $table->string('address', 200)->nullable();
            $table->string('address2', 200)->nullable();
            $table->string('suburb', 150)->nullable();
            $table->string('state', 40)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('country', 100)->nullable();

            $table->string('photo', 250)->nullable();

            // Client fields
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('client_phone', 25)->nullable();
            $table->string('client_phone_desc', 25)->nullable();
            $table->string('client_phone2', 25)->nullable();
            $table->string('client_phone2_desc', 25)->nullable();


            // Admin fields
            $table->dateTime('contract_sent')->nullable();
            $table->dateTime('contract_signed')->nullable();
            $table->dateTime('deposit_paid')->nullable();
            $table->dateTime('completion_signed')->nullable();
            $table->tinyInteger('engineering')->default(0);
            $table->tinyInteger('construction')->default(0);
            $table->tinyInteger('hbcf')->default(0);
            $table->string('consultant_name', 50)->nullable();


            // Extra fields
            $table->timestamp('completed')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();


            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });


        // Each Site is assigned multiple supervisor from Company
        Schema::create('site_supervisor', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Site Docs
        //
        Schema::create('site_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10)->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('site_id')->unsigned()->nullable();
            $table->string('name', 100)->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('reference', 50)->nullable();
            $table->string('version', 10)->nullable();
            $table->tinyInteger('private')->default(0);
            $table->text('notes')->nullable();
            $table->string('share', 2)->default('b');
            $table->tinyInteger('status')->default(1);
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->default(0);

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys');
            $table->foreign('for_company_id')->references('id')->on('companys');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_docs_categories', function (Blueprint $table) {
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
            $table->softDeletes();
        });

        //
        // Site Hazards
        //
        Schema::create('site_hazards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();

            $table->text('reason')->nullable();
            $table->tinyInteger('action_required')->default(0);
            $table->text('location')->nullable();
            $table->string('source', 250)->nullable();
            $table->tinyInteger('rating')->default(0);
            $table->tinyInteger('failure')->default(0);
            $table->string('attachment', 250)->nullable();
            $table->text('notes')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->dateTime('resolved_at')->nullable();

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Site Accidents
        //
        Schema::create('site_accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->string('supervisor')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('age', 10)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->text('location')->nullable();
            $table->text('nature')->nullable();
            $table->string('referred', 50)->nullable();
            $table->string('damage', 100)->nullable();
            $table->text('info')->nullable();
            $table->text('action')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->dateTime('resolved_at')->nullable();

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Site Asbestos
        //
        Schema::create('site_asbestos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->nullable();
            $table->string('amount', 10)->nullable();
            $table->tinyInteger('friable');
            $table->string('type', 255)->nullable();
            $table->text('location')->nullable();
            $table->dateTime('date_from')->nullable();
            $table->dateTime('date_to')->nullable();
            $table->string('hours_from', 50)->nullable();
            $table->string('hours_to', 50)->nullable();
            $table->string('workers', 10)->nullable();
            $table->tinyInteger('equip_overalls')->default(0);
            $table->tinyInteger('equip_mask')->default(0);
            $table->tinyInteger('equip_gloves')->default(0);
            $table->tinyInteger('equip_half_face')->default(0);
            $table->tinyInteger('equip_full_face')->default(0);
            $table->string('equip_other', 255)->nullable();
            $table->tinyInteger('method_fencing')->default(0);
            $table->tinyInteger('method_signage')->default(0);
            $table->tinyInteger('method_water')->default(0);
            $table->tinyInteger('method_pva')->default(0);
            $table->tinyInteger('method_barriers')->default(0);
            $table->tinyInteger('method_plastic')->default(0);
            $table->tinyInteger('method_vacuum')->default(0);
            $table->string('method_other', 255)->nullable();
            $table->text('isolation')->nullable();
            $table->tinyInteger('register')->default(0);
            $table->tinyInteger('swms')->default(0);
            $table->tinyInteger('inspection')->default(0);
            $table->integer('supervisor_id')->unsigned()->nullable();
            $table->string('attachment', 255)->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(1);
            $table->dateTime('resolved_at')->nullable();

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Site QA
        //
        Schema::create('site_qa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->integer('site_id')->unsigned()->nullable();
            $table->string('version', 10)->nullable();
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();
            $table->integer('supervisor_sign_by')->unsigned()->nullable();
            $table->timestamp('supervisor_sign_at')->nullable();
            $table->integer('manager_sign_by')->unsigned();
            $table->timestamp('manager_sign_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('share', 2)->default('b');
            $table->tinyInteger('status')->default(0);
            $table->integer('company_id')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_qa_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doc_id')->unsigned();
            $table->integer('task_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->integer('order')->unsigned();
            $table->tinyInteger('super')->default(0);
            $table->tinyInteger('master')->default(0);
            $table->integer('master_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('sign_by')->unsigned()->nullable();
            $table->timestamp('sign_at')->nullable();
            $table->integer('done_by')->unsigned()->nullable();

            // Foreign keys
            $table->foreign('doc_id')->references('id')->on('site_qa')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Actions
        //
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table', 50)->nullable();
            $table->integer('table_id')->unsigned();
            $table->integer('todo_id')->unsigned()->nullable();
            $table->text('action')->nullable();
            $table->string('attachment', 250)->nullable();

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('updated_by')->unsigned();
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
        Schema::dropIfExists('actions');
        Schema::dropIfExists('site_qa_items');
        Schema::dropIfExists('site_qa');
        Schema::dropIfExists('site_asbestos');
        Schema::dropIfExists('site_accidents');
        Schema::dropIfExists('site_hazards');
        Schema::dropIfExists('site_docs_categories');
        Schema::dropIfExists('site_docs');
        Schema::dropIfExists('site_supervisor');
        Schema::dropIfExists('sites');
    }
}
