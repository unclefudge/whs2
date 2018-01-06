<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportCommsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Support Tickets
        //
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->default(0);
            $table->string('name', 250);
            $table->text('summary');
            $table->tinyInteger('priority')->default(0);
            $table->dateTime('eta')->nullable();
            $table->integer('hours')->default(0);
            $table->string('attachment', 250);
            $table->text('notes');
            $table->tinyInteger('status')->default(1);
            $table->dateTime('resolved_at')->nullable();

            // Foreign keys
            $table->integer('company_id')->unsigned()->default(0);
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('support_tickets_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->unsigned();
            $table->text('action');
            $table->string('attachment', 250);

            // Foreign keys
            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Notify
        //
        Schema::create('notify', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->text('info');
            $table->string('type', 50);
            $table->integer('type_id')->unsigned();
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->tinyInteger('priority')->default(0);
            $table->string('action', 25);
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

        Schema::create('notify_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notify_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('opened')->default(0);
            $table->dateTime('opened_at')->nullable();

            // Foreign keys
            $table->foreign('notify_id')->references('id')->on('notify')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        //
        // Todoo
        //
        Schema::create('todo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->text('info');
            $table->string('type', 50);
            $table->integer('type_id')->unsigned();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('done_at')->nullable();
            $table->integer('done_by')->unsigned();
            $table->tinyInteger('priority')->default(0);
            $table->string('attachment', 250);
            $table->text('comments');
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

        Schema::create('todo_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todo_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('opened')->default(0);
            $table->dateTime('opened_at')->nullable();

            // Foreign keys
            $table->foreign('todo_id')->references('id')->on('todo')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        //
        // Settings
        //
        Schema::create('settings_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->nullable();
            $table->integer('user_id')->unsigned()->default(0);
            $table->integer('company_id')->unsigned()->default(0);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('settings_notifications');
        Schema::dropIfExists('todo_user');
        Schema::dropIfExists('todo');
        Schema::dropIfExists('notify_user');
        Schema::dropIfExists('notify');
        Schema::dropIfExists('support_tickets_actions');
        Schema::dropIfExists('support_tickets');
    }
}
