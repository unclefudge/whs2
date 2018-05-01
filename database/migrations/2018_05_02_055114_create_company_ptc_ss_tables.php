<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyPtcSsTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Company Period Trade Contract
        //
        Schema::create('company_doc_ptc', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date')->nullable();
            $table->integer('period')->unsigned();
            $table->dateTime('expiry')->nullable();
            $table->integer('principle_id')->unsigned();
            $table->string('principle_name', 250)->nullable();
            $table->string('principle_address', 250)->nullable();
            $table->string('principle_phone', 50)->nullable();
            $table->string('principle_email')->nullable();
            $table->string('principle_abn', 20)->nullable();
            $table->string('principle_hia', 25)->nullable();
            $table->integer('principle_signed_id')->unsigned();
            $table->timestamp('principle_signed_at')->nullable();
            $table->integer('contractor_id')->unsigned();
            $table->string('contractor_name', 250)->nullable();
            $table->string('contractor_address', 250)->nullable();
            $table->string('contractor_phone', 50)->nullable();
            $table->string('contractor_email')->nullable();
            $table->string('contractor_abn', 20)->nullable();
            $table->tinyInteger('contractor_gst')->nullable();
            $table->string('contractor_licence', 25);
            $table->string('contractor_pl_name', 100);
            $table->string('contractor_pl_ref', 50);
            $table->dateTime('contractor_pl_expiry')->nullable();
            $table->string('contractor_wc_name', 100);
            $table->string('contractor_wc_ref', 50);
            $table->dateTime('contractor_wc_expiry')->nullable();
            $table->string('contractor_sa_name', 100);
            $table->string('contractor_sa_ref', 50);
            $table->dateTime('contractor_sa_expiry')->nullable();
            $table->integer('contractor_signed_id')->unsigned();
            $table->timestamp('contractor_signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('share', 2)->default('b');
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

        //
        // Company Subcontractors Statement
        //
        Schema::create('company_doc_ss', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->integer('contractor_id')->unsigned();
            $table->string('contractor_name', 250)->nullable();
            $table->string('contractor_address', 250)->nullable();
            $table->string('contractor_phone', 50)->nullable();
            $table->string('contractor_email')->nullable();
            $table->string('contractor_abn', 20)->nullable();
            $table->string('contractor_signed_name')->nullable();
            $table->string('contractor_signed_title', 250)->nullable();
            $table->integer('contractor_signed_id')->unsigned();
            $table->timestamp('contractor_signed_at')->nullable();
            $table->integer('principle_id')->unsigned();
            $table->string('principle_name', 250)->nullable();
            $table->string('principle_abn', 20)->nullable();
            $table->integer('principle_signed_id')->unsigned();
            $table->timestamp('principle_signed_at')->nullable();
            $table->string('contract_no', 250);
            $table->dateTime('claim_payment')->nullable();
            $table->tinyInteger('clause_a1')->default(0);
            $table->tinyInteger('clause_a2')->default(0);
            $table->dateTime('wc_date')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('share', 2)->default('b');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_doc_ss');
        Schema::dropIfExists('company_doc_ptc');
    }
}
