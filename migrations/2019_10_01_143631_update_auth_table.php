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
	    	$table->dropColumn('shop_id');
	    	$table->string('shop_id', 100)->after('id')->index('shop_id');
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
	        $table->dropColumn('shop_id');
	        $table->bigInteger('shop_id')->index('shop_id')->after('id');
        	$table->dropColumn('app_installation_id');
        });
    }
}
