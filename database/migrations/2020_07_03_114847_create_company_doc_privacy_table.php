<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDocPrivacyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Company Privacy Policy
        //
        Schema::create('company_doc_privacy', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->text('contractor_signed_name')->nullable();
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_doc_privacy');
    }
}
