<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('shopify_apps', function (Blueprint $table) {
	        $table->string('app_installation_id', 100)->after('id')->index('app_installation_id');
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
