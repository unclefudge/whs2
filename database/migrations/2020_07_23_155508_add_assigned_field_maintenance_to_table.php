<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignedFieldMaintenanceToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_maintenance', function (Blueprint $table) {
            $table->integer('assigned_to')->unsigned()->nullable();
            $table->tinyInteger('further_works')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_maintenance', function($table) {
            $table->dropColumn('further_works');
            $table->dropColumn('assigned_to');
        });
    }
}
