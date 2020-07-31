<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDoneAtFieldToMaintenanceitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_maintenance_items', function (Blueprint $table) {
            $table->timestamp('done_at')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->integer('task_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_maintenance_items', function($table) {
            $table->dropColumn('task_id');
            $table->dropColumn('attachment');
            $table->dropColumn('done_at');
        });
    }
}
