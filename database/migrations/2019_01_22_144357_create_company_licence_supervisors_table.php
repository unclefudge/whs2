<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLicenceSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_licence_supervisors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doc_id')->unsigned()->nullable();
            $table->integer('super')->unsigned()->nullable();
            $table->integer('licence_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
            $table->foreign('doc_id')->references('id')->on('company_docs')->onDelete('cascade');

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
        Schema::dropIfExists('contractor_licence_supervisors');
    }
}
