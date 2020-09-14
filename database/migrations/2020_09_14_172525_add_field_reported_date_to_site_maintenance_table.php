<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldReportedDateToSiteMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_maintenance', function (Blueprint $table) {
            $table->dateTime('reported')->nullable();
            $table->dateTime('resolved')->nullable();
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
            $table->dropColumn('resolved');
            $table->dropColumn('reported');
        });
    }
}
