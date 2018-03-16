<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFieldLicenceRequiredCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companys', function (Blueprint $table) {
            //$table->tinyInteger('licence_required')->default(0)->change();
            $table->renameColumn('licence_required', 'lic_override');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companys', function (Blueprint $table) {
            $table->renameColumn('lic_override', 'licence_required');
            //$table->tinyInteger('licence_required')->default(1)->change();
        });
    }
}
