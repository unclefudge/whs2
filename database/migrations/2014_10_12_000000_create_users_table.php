<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50)->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password', 60);

            // Contact Details
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('address', 200);
            $table->string('address2', 200);
            $table->string('suburb', 150);
            $table->string('state', 40);
            $table->string('postcode', 10);
            $table->string('country', 100);
            $table->string('phone');

            // Additional fields
            $table->tinyInteger('employment_type')->default(0);
            $table->tinyInteger('subcontractor_type')->default(0);
            $table->string('photo', 250);
            $table->text('notes');

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->integer('client_id')->unsigned()->default(0);

            $table->tinyInteger('security')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('password_reset')->default(1);
            $table->rememberToken();
            $table->string('last_ip', 25);
            $table->timestamp('last_login');

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
        Schema::dropIfExists('users');
    }
}
