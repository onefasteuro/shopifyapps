<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('shopify_apps', function (Blueprint $table) {
	        $table->bigInteger('app_installation_id')->after('id')->index('app_installation_id');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopify_apps', function (Blueprint $table) {
        	$table->dropColumn('app_installation_id');
        });
    }
}
