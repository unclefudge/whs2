<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePlannerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Planner
        //
        Schema::create('site_planner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->dateTime('from')->index();
            $table->dateTime('to')->index();
            $table->integer('days')->unsigned();
            $table->string('entity_type', 10)->nullable();
            $table->integer('entity_id')->unsigned()->index();
            $table->integer('task_id')->unsigned()->nullable();
            $table->tinyInteger('weekend')->default(0);

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('trade_task')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Roster
        //
        Schema::create('site_roster', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->dateTime('date')->index();

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        //
        // Attendance
        //
        Schema::create('site_attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamp('date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->tinyInteger('safe_site')->default(0);
            $table->tinyInteger('accept_whs')->default(1);

            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        //
        // Compliance
        //
        Schema::create('site_compliance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamp('date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->integer('reason')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('archive')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamp('resolved_at')->nullable();


            // Foreign keys
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Modify info
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_compliance_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->tinyInteger('status')->default(1);

            // Foreign keys
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_compliance_reasons');
        Schema::dropIfExists('site_compliance');
        Schema::dropIfExists('site_attendance');
        Schema::dropIfExists('site_roster');
        Schema::dropIfExists('site_planner');
    }
}
