<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone', 50);

            // Address
            $table->string('address', 200);
            $table->string('address2', 200);
            $table->string('suburb', 150);
            $table->string('state', 40);
            $table->string('postcode', 10);
            $table->string('country', 100);

            $table->text('notes');
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');


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
        Schema::dropIfExists('clients');
    }
}
