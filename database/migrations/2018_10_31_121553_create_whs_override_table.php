<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhsOverrideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_override', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 10)->nullable();
            $table->tinyInteger('required')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('for_company_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('expiry')->nullable();
            $table->tinyInteger('status')->default(1);

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
        Schema::dropIfExists('compliance_override');
    }
}
